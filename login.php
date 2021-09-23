<?php

class login
{
    protected $loginTypes = [
        'checker' => 'Checker',
        'admin' => 'Admin',
        'adviser' => 'Adviser',
        'bdm' => 'BDM',
        'telemarketer' => 'Telemarketer',
    ];

    protected $userTypeIds = [
        'admin' => 4,
        'adviser' => 2,
        'bdm' => 5,
        'telemarketer' => 6,
    ];

    protected $session;

    protected $app;

    protected $login;

    protected $test;

    protected $training;

    public function __construct()
    {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include_once('lib/Session.helper.php');
        include_once('lib/General.helper.php');
        include_once('lib/Login.controller.php');
        include_once('lib/Test.controller.php');
        include_once('lib/Training.controller.php');

        $this->session = new SessionHelper();
        $this->app = new GeneralHelper();
        $this->login = new LoginController();
        $this->test = new TestController();
        $this->training = new TrainingController();

        $_POST ? $this->post() : $this->pre();
    }

    protected function formValidated()
    {
        $_POST['inputErrors'] = [];

        if (! in_array(trim($_POST['login_type'] ?? ''), array_keys($this->loginTypes))) {
            array_push($_POST['inputErrors'], 'Please provide a valid value for Login as.');
        }

        if (! trim($_POST['email'] ?? '')) {
            array_push($_POST['inputErrors'], 'Please provide a value for Email Address.');
        }

        if (! trim($_POST['password'] ?? '')) {
            array_push($_POST['inputErrors'], 'Please provide a value for Password.');
        }

        if (in_array(trim($_POST['login_type'] ?? ''), ['admin', 'bdm', 'telemarketer']) && ! trim($_POST['first_name'] ?? '')) {
            array_push($_POST['inputErrors'], 'Please provide a value for First Name.');
        }

        if (in_array(trim($_POST['login_type'] ?? ''), ['admin', 'bdm', 'telemarketer']) && ! trim($_POST['last_name'] ?? '')) {
            array_push($_POST['inputErrors'], 'Please provide a value for Last Name.');
        }

        if (in_array(trim($_POST['login_type'] ?? ''), ['bdm', 'telemarketer']) && ! trim($_POST['venue'] ?? '')) {
            array_push($_POST['inputErrors'], 'Please provide a value for Venue.');
        }

        if (count($_POST['inputErrors'] ?? [])) {
            return false;
        }

        return true;
    }

    protected function login()
    {
        $_POST['inputErrors'] = [];

        if ('checker' == $_POST['login_type']) {
            $users = $this->login->userLogin($_POST['email'], $_POST['password']);

            $error = $users['message'] ?? '';

            if ($error) {
                array_push($_POST['inputErrors'], $error);
            } else {
                $this->session->createSession($users[0]);

                header('location: index.php');
            }
        }

        if ('adviser' == $_POST['login_type'] && ($_GET['type'] ?? '') != 'adviser') {
            $user = $this->training->trainingLogin($_POST['email'], $_POST['password'])->fetch_assoc();

            if (! $user) {
                array_push($_POST['inputErrors'], 'Email Address and Password do not match.');
            }

            if ('0' == $user['status']) {
                array_push($_POST['inputErrors'], 'Account is deactivated.');
            }

            if ($_POST['password'] != $user['password']) {
                array_push($_POST['inputErrors'], 'Email Address and Password do not match.');
            }

            if (! count($_POST['inputErrors'] ?? [])) {
                $_SESSION['full_name'] = $user['first_name'] . $user['last_name'];
                $_SESSION['fsp'] = $user['ssf_number'];
                $_SESSION['email'] = $user['email_address'];
                $_SESSION['id_user_type'] = $user['id_user_type'];
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['grant'] = 'yes';

                $training_details = $this->test->userCheck($user['email_address'], $user['id_user_type']);
                $training_details = $training_details->fetch_assoc();

                $location = 'training?page=adviser_profile&id=' . $user['id_user'] . '&email=' . $user['email_address'] . '&user_type=' . $user['id_user_type'];

                $data[] = $training_details;

                if ($this->session->createTemporarySession($data)) {
                    if (in_array($user['id_user_type'], [1, 3])) {
                        header('location: training?page=training_list');
                    } else {
                        header('location:' . $location);
                    }
                }
            }
        }

        $userTypeId = $this->userTypeIds[$_POST['login_type']];

        $correctPassword = $this->test->getReferralCode($userTypeId)->fetch_assoc()['password'];

        if (! in_array($userTypeId, [2, 7, 8])) {
            if ($_POST['password'] != $correctPassword) {
                array_push($_POST['inputErrors'], 'Invalid referral code.');
            }

            $user = $this->test->userAdd($_POST['email'], $_POST['password'], $_POST['first_name'], $_POST['last_name'], $userTypeId)->fetch_assoc();

            if (! $user) {
                array_push($_POST['inputErrors'], 'Something went wrong. Please try again.');
            }

            if (! count($_POST['inputErrors'])) {
                $user['venue'] = $_POST['venue'];

                $this->session->createTemporarySession([$user]);

                header('location: test.php?page=test_set');
            }
        }

        if (count($_POST['inputErrors'] ?? [])) {
            $this->pre();
        }
    }

    protected function getLoginType()
    {
        $type = $_GET['type'] ?? '';

        if ('trainer' == $type) {
            return 'adviser';
        }

        return $type;
    }

    protected function pre()
    {
        if ($_GET['action'] ?? '' == 'logout') {
            $this->session->destroySession();
        }

        if ($this->session->isSessionActive()) {
            header('location: index.php');
        } ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>EliteInsure Portal | Login</title>

            <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss-colors-css-variables@1.1.2-rc.2/css/tailwindcss-colors.min.css" rel="stylesheet">

            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Quattrocento+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

            <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

            <style>
                .bg-shark {
                    background-color: #2B3036
                }

                .bg-lmara {
                    background-color: #0081B8
                }

                .bg-tblue {
                    background-color: #0F6497
                }

                .bg-dsgreen {
                    background-color: #0C4664
                }

                .text-shark {
                    color: #2B3036
                }

                .text-lmara {
                    color: #0081B8
                }

                .text-tblue {
                    color: #0F6497
                }

                .text-dsgreen {
                    color: #0C4664
                }

            </style>
        </head>

        <body style="font-family: 'Quattrocento Sans';">
            <div class="min-h-screen bg-white flex">
                <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
                    <div class="mx-auto w-full max-w-sm lg:w-96">
                        <div>
                            <img class="h-12 w-auto" src="img/elitelogo.png" alt="EliteInsure">
                            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                                Sign in to your account
                            </h2>
                        </div>

                        <div class="mt-8"
                            x-data="{
                                login_type: '<?php echo $_POST['login_type'] ?? $this->getLoginType(); ?>',
                                email: '<?php echo $_POST['email'] ?? ''; ?>',
                                first_name: '<?php echo $_POST['first_name'] ?? ''; ?>',
                                last_name: '<?php echo $_POST['last_name'] ?? ''; ?>',
                                venue: '<?php echo $_POST['venue'] ?? ''; ?>',
                            }">

                            <?php if (count($_POST['inputErrors'] ?? [])) { ?>
                                <div>
                                    <div>
                                        <div class="mt-1">
                                            <div class="rounded-md bg-red-50 p-4">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                    </svg>
                                                    </div>
                                                    <div class="ml-3">
                                                    <h3 class="text-sm font-bold text-red-800">
                                                        Could not sign in to your account.
                                                    </h3>
                                                    <div class="mt-2 text-sm text-red-700">
                                                        <ul role="list" class="list-disc pl-5 space-y-1">
                                                        <?php foreach ($_POST['inputErrors'] as $inputError) { ?>
                                                            <li><?php echo $inputError; ?></li>
                                                        <?php } ?>
                                                        </ul>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6 relative">
                                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                            <div class="w-full border-t border-gray-300"></div>
                                        </div>
                                        <div class="relative flex justify-center text-sm">
                                            <span class="px-2 bg-white text-gray-500"></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="mt-6">
                                <form action="" method="POST" class="space-y-3">
                                    <div>
                                        <div>
                                            <select id="login_type" name="login_type" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                                x-model="login_type"
                                                >
                                                <option value="">Login as</option>
                                                <?php foreach ($this->loginTypes as $value => $label) { ?>
                                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <div>
                                            <input id="email" name="email" type="email" autocomplete="email" placeholder="Email Address" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                                x-model="email"
                                            >
                                        </div>
                                    </div>

                                    <div x-show="['admin', 'bdm', 'telemarketer'].includes(login_type)">
                                        <div>
                                            <input id="first_name" name="first_name" type="text" placeholder="First Name" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                                x-model="first_name"
                                            >
                                        </div>
                                    </div>

                                    <div x-show="['admin', 'bdm', 'telemarketer'].includes(login_type)">
                                        <div>
                                            <input id="last_name" name="last_name" type="text" placeholder="Last Name" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                                x-model="last_name"
                                            >
                                        </div>
                                    </div>

                                    <div x-show="['bdm', 'telemarketer'].includes(login_type)">
                                        <div>
                                            <input id="venue" name="venue" type="text" placeholder="Venue" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm"
                                                x-model="venue"
                                            >
                                        </div>
                                    </div>

                                    <div>
                                        <div>
                                            <input id="password" name="password" type="password" placeholder="Password" autocomplete="current-password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-gray-500 focus:border-gray-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <button type="submit"
                                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-tblue hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                            Sign in
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="hidden lg:block relative w-0 flex-1 bg-dsgreen">
                    <img class="absolute inset-0 h-full w-full object-cover mix-blend-luminosity" src="img/login-background.jpg" alt="">
                </div>
            </div>
        </body>

        </html>
        <?php
    }

    protected function post()
    {
        if (! $this->formValidated()) {
            $this->pre();

            return;
        }

        $this->login();
    }
}

new login();
