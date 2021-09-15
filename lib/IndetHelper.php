<?php

$autoloadPath = realpath(__DIR__ . '/../package/vendor/autoload.php');

require_once $autoloadPath;

include_once('class/DB.class.php');

use Carbon\Carbon;

class IndetHelper extends DB
{
    public $dbName;

    public function __construct()
    {
        parent::__construct();

        $path = realpath(__DIR__ . '/class/conf/conf.ini');

        $conf = parse_ini_file($path);

        $this->dbName = $conf['indet_db_name'];
    }

    public function listDeals($email = null)
    {
        $query = "SELECT id FROM $this->dbName.adviser_tbl WHERE email = '$email'";

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

                $collection->push($deal);
            }
        }

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

        $now = Carbon::now();

        if (in_array($now->format('j'), range(1, 15))) {
            $currentPeriod = [
                'from' => $now->startOfMonth()->format('Ymd'),
                'to' => $now->format('Ym15'),
            ];

            $previous = $now->subMonths(1);

            $previousPeriod = [
                'from' => $previous->format('Ym16'),
                'to' => $previous->endOfMonth()->format('Ymd'),
            ];
        } else {
            $currentPeriod = [
                'from' => $now->format('Ym16'),
                'to' => $now->endOfMonth()->format('Ym15'),
            ];

            $previousPeriod = [
                'from' => $now->startOfMonth()->format('Ymd'),
                'to' => $now->format('Ym15'),
            ];
        }

        $currentPeriod['fromDate'] = Carbon::createFromFormat('Ymd', $currentPeriod['from']);
        $currentPeriod['toDate'] = Carbon::createFromFormat('Ymd', $currentPeriod['to']);
        $previousPeriod['fromDate'] = Carbon::createFromFormat('Ymd', $previousPeriod['from']);
        $previousPeriod['toDate'] = Carbon::createFromFormat('Ymd', $previousPeriod['to']);

        $currentDeals = $deals->where('date_issued', '<=', $currentPeriod['to'])->values();

        $previousDeals = $deals->where('date_issued', '<=', $previousPeriod['to'])->values();

        return [
            'currentPeriod' => $currentPeriod,
            'currentDeals' => $currentDeals,
            'previousPeriod' => $previousPeriod,
            'previousDeals' => $previousDeals,
        ];
    }
}
