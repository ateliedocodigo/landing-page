<?php

/**
 * MailChimp API documentation:
 * http://apidocs.mailchimp.com/api/
 */

require_once 'vendor/autoload.php'; # Download: http://apidocs.mailchimp.com/api/downloads/

use Schnittstabil\Csrf\TokenService\TokenService;

// Config default
$config_default = [
    'apiKey' => 'default',
    'listId' => 'default',
    // Shared secret key used for generating and validating token signatures:
    'csrfKey' => 'ebec1745-f91a-44f5-8db7-373cb64d7eb7',
    // Time to Live in seconds; default is 1440 seconds === 24 minutes:
    'csrfTtl' => 1440,
];
try {
    if (!is_file('config.php')) {
        throw new Exception("Config file not found!");
    }
    $config = include 'config.php';
} catch (Exception $e) {
    $config = [];
}
$config = array_merge($config_default, $config);

# Configuration
$email = isset($_POST['email']) ? $_POST['email'] : '';
$token = isset($_POST['token']) ? $_POST['token'] : '';

$api = new Mailchimp($config['apiKey'], ['debug' => true]);

// create the TokenService
$tokenService = new TokenService($config['csrfKey'], $config['csrfTtl']);

// validate the token - stateless; no session needed
if (!empty($email) && !$tokenService->validate($token)) {
    http_response_code(403);
    echo '<h2>403 Access Forbidden, bad CSRF token</h2>';
    exit();
}
// generate a URL-safe token
$token = $tokenService->generate();

// echo "<pre>";
// var_dump($api->lists->getList()); # Uncomment this to get ID's of mailing lists and select proper list ID.
// echo "</pre>";
?>
<!doctype html>
<html lang="pt_BR">
<head>
    <title>Ateliê do Código</title>
    <meta charset="utf-8" />
    <link rel="shortcut icon" type="image/png" href="images/favicon-16x16.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="assets/css/main.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->
    <!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-84648519-1', 'auto');
  ga('send', 'pageview');
</script>
</head>
<body>

    <!-- Header -->
    <header id="header">
        <h1>Você é um artesão de software?</h1>
        <p>Você é um artesão de software? <br />
        Se interessa por novas tecnologias e tutorias de programação e boas práticas de código? <br />
        Então deixe seu email e não perca nenhuma atualização.<br />
        </p>
    </header>

    <!-- Signup Form -->
    <form id="signup-form" method="post" action="#">
        <input type="email" name="email" id="email" placeholder="Email" required value="<?php echo $email; ?>" />
        <input type="submit" value="Quero receber!" />
        <input type="hidden" name="token" value="<?php echo $token; ?>">
<?php if (filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
    <?php
        try {
            $api->lists->subscribe($config['listId'], array('email' => $email, 'update_existing' => true));
            echo '<span class="visible message success">Confirma no teu email a inscrição por favor.</span>';
        } catch (Mailchimp_Invalid_Email $e) {
            echo '<span class="visible message failure">Coloca um email válido por favor!</span>';
            $api->log($e->getMessage());
        } catch (Mailchimp_List_AlreadySubscribed $e) {
            echo '<span class="visible message success">Teu email já está cadastrado. Valeu!</span>';
            $api->log($e->getMessage());
        } catch (Mailchimp_Invalid_ApiKey $e) {
            echo '<span class="visible message failure">Ocorreu um erro, tente mais tarde</span>';
            $api->log($e->getMessage());
        } catch (Mailchimp_Error $e) {
            echo '<span class="visible message failure">Coloca um email válido por favor!!</span>';
            $api->log($e->getMessage());
        }
    ?>
<?php endif;
?>
    </form>

    <!-- Footer -->
    <footer id="footer">
        <ul class="icons">
            <li><a href="https://www.youtube.com/channel/UCJtkz7su6iT_jZva5RoSZiQ" target="_blank" class="icon fa-youtube"><span class="label">Instagram</span></a></li>
            <li><a href="http://fb.com/ateliedocodigo" target="_blank" class="icon fa-facebook"><span class="label">Instagram</span></a></li>
            <li><a href="http://twitter.com/ateliedocodigo" target="_blank" class="icon fa-twitter"><span class="label">Twitter</span></a></li>
            <li><a href="https://github.com/ateliedocodigo" target="_blank" class="icon fa-github"><span class="label">GitHub</span></a></li>
        </ul>
        <ul class="copyright">
            <li>&copy; Ateliê do Código.</li>
        </ul>
    </footer>

    <!-- Scripts -->
    <!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
    <script src="assets/js/main.js"></script>

</body>
</html>
