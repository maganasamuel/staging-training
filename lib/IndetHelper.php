<?php
error_reporting(E_ERROR | E_PARSE);

$autoloadPath = realpath(__DIR__ . '/../package/vendor/autoload.php');

require_once $autoloadPath;

include_once('class/DB.class.php');

use Carbon\Carbon;

class IndetHelper extends DB
{
    public $dbName;

    protected $email;

    protected $deals;

    public function __construct($email)
    {
        parent::__construct();

        $path = realpath(__DIR__ . '/class/conf/conf.ini');

        $conf = parse_ini_file($path);

        $this->dbName = $conf['indet_db_name'];

        $this->email = $email;

        $this->listDeals();
    }

    public function listPendingIssuedPolicies()
    {
        $collection = $this->listDeals();

        $deals = $collection->where('status', 'Issued')
            ->where('commission_status', 'Not Paid')
            ->map(function ($deal) {
                return collect($deal)->only([
                    'client_name_life_insured',
                    'policy_number',
                    'company',
                    'date_issued',
                    'issued_api',
                    'record_keeping',
                    'compliance_status',
                    'audit_status',
                    'notes',
                    'status',
                ])->all();
            })->sortBy('date_issued')
            ->values();

        $now = Carbon::now('UTC');

        $now->setTimezone('Pacific/Auckland');

        if (in_array($now->format('j'), range(1, 15))) {
            $currentPeriod = [
                'from' => $now->copy()->startOfMonth(),
                'to' => $now->copy()->startOfMonth()->addDays(14),
            ];

            $previous = $now->copy()->subMonths(1);

            $previousPeriod = [
                'from' => $previous->copy()->startOfMonth()->addDays(15),
                'to' => $previous->copy()->endOfMonth(),
            ];
        } else {
            $currentPeriod = [
                'from' => $now->copy()->startOfMonth()->addDays(15),
                'to' => $now->copy()->endOfMonth(),
            ];

            $previousPeriod = [
                'from' => $now->copy()->startOfMonth(),
                'to' => $now->copy()->startOfMonth()->addDays(14),
            ];
        }

        $currentDeals = $deals->where('date_issued', '<=', $currentPeriod['to']->format('Ymd'))->values();

        $previousDeals = $deals->where('date_issued', '<=', $previousPeriod['to']->format('Ymd'))->values();

        return [
            'currentPeriod' => $currentPeriod,
            'currentDeals' => $currentDeals,
            'previousPeriod' => $previousPeriod,
            'previousDeals' => $previousDeals,
        ];
    }

    public function listClawbacks()
    {
        $collection = $this->listDeals();

        $deals = $collection->where('status', 'Issued')
            ->where('clawback_status', '!=', 'None')
            ->where('refund_status', 'No')
            ->map(function ($deal) {
                return collect($deal)->only([
                    'client_name_life_insured',
                    'policy_number',
                    'company',
                    'date_issued',
                    'clawback_date',
                    'clawback_api',
                    'clawback_status',
                    'clawback_notes',
                ])->all();
            })->sortBy('clawback_date')
            ->values();

        $now = Carbon::now('UTC');

        $now->setTimezone('Pacific/Auckland');

        if (in_array($now->format('j'), range(1, 15))) {
            $currentPeriod = [
                'from' => $now->copy()->startOfMonth(),
                'to' => $now->copy()->startOfMonth()->addDays(14),
            ];

            $previous = $now->copy()->subMonths(1);

            $previousPeriod = [
                'from' => $previous->copy()->startOfMonth()->addDays(15),
                'to' => $previous->copy()->endOfMonth(),
            ];
        } else {
            $currentPeriod = [
                'from' => $now->copy()->startOfMonth()->addDays(15),
                'to' => $now->copy()->endOfMonth(),
            ];

            $previousPeriod = [
                'from' => $now->copy()->startOfMonth(),
                'to' => $now->copy()->startOfMonth()->addDays(14),
            ];
        }

        $currentDeals = $deals->where('clawback_date', '<=', $currentPeriod['to']->format('Ymd'))->values();

        $previousDeals = $deals->where('clawback_date', '<=', $previousPeriod['to']->format('Ymd'))->values();

        return [
            'currentPeriod' => $currentPeriod,
            'currentDeals' => $currentDeals,
            'previousPeriod' => $previousPeriod,
            'previousDeals' => $previousDeals,
        ];
    }

    public function listArrears()
    {
        $collection = $this->listDeals();

        return $collection->whereNotNull('arrear_status')->values();
    }

    public function listSubmittedDeals()
    {
        $query = "SELECT id FROM $this->dbName.adviser_tbl WHERE email = '$this->email'";

        $adviser_id = $this->execute($this->prepare($query))->fetch_assoc()['id'];

        $query = "SELECT
                    s.id AS id,
                    s.deals AS deals,
                    s.timestamp,
                    c.id AS client_id,
                    c.name AS client_name,
                    l.name AS leadgen_name,
                    a.name AS adviser_name,
                    s.deals
                FROM $this->dbName.submission_clients s
                LEFT JOIN $this->dbName.clients_tbl c ON s.client_id = c.id
                LEFT JOIN $this->dbName.leadgen_tbl l ON c.leadgen = l.id
                LEFT JOIN $this->dbName.adviser_tbl a ON c.assigned_to = a.id
                WHERE
                    s.client_id NOT IN (
                        SELECT name FROM $this->dbName.issued_clients_tbl
                    )
                    AND c.assigned_to = '$adviser_id'
                ORDER BY s.timestamp DESC";

        $result = $this->execute($this->prepare($query));

        $collection = collect([]);

        while ($row = $result->fetch_assoc()) {
            if (! isset($row['deals'])) {
                continue;
            }

            $deals = json_decode($row['deals']);

            foreach ($deals as $deal) {
                $deal->client_name_life_insured = $row['client_name'] . ($deal->life_insured ? (', ' . $deal->life_insured) : '');

                $collection->push($deal);
            }
        }

        return $collection;
    }

    protected function listDeals()
    {
        $query = "SELECT id FROM $this->dbName.adviser_tbl WHERE email = '$this->email'";

        $adviser_id = $this->execute($this->prepare($query))->fetch_assoc()['id'];

        $query = "SELECT
				*,
				c.name as client_name,
				l.name as source
			FROM $this->dbName.clients_tbl c
			LEFT JOIN $this->dbName.submission_clients s ON c.id = s.client_id
			LEFT JOIN $this->dbName.leadgen_tbl l ON l.id = c.leadgen
			WHERE
				assigned_to='$adviser_id'
				AND c.status != 'Cancelled'";

        $result = $this->execute($this->prepare($query));

        $collection = collect([]);

        while ($row = $result->fetch_assoc()) {
            if (! isset($row['deals'])) {
                continue;
            }

            $deals = json_decode($row['deals']);

            foreach ($deals as $deal) {
                $deal->client_name_life_insured = $row['client_name'] . ($deal->life_insured ? (', ' . $deal->life_insured) : '');
                $deal->audit_status = $deal->audit_status ?? 'Pending';
                $deal->refund_status = $deal->refund_status ?? 'No';

                $collection->push($deal);
            }
        }

        return $collection;
    }
}
