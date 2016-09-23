<?php

/**
 * MailChimp API documentation:
 * http://apidocs.mailchimp.com/api/
 */

require_once 'vendor/autoload.php'; # Download: http://apidocs.mailchimp.com/api/downloads/

try {
    $config = include 'config.php';
} catch (Exception $e) {
    $config = [];
}
# Configuration
$email = isset($_POST['email']) ? $_POST['email'] : '';

$api = new Mailchimp($config['apiKey'], ['debug' => true]);

// echo "<pre>";
// var_dump($api->lists->getList()); # Uncomment this to get ID's of mailing lists and select proper list ID.
// echo "</pre>";
?>
<!doctype html>
<html lang="pt_BR">
<head>
    <title>Ateliê do Código</title>
    <meta charset="utf-8" />
    <style>
        body {
            background-color: rgba(34, 34, 34, 1);
            color: #fff;
        }
        .email-form-wrap {
            background: linear-gradient(#3e9be2, #2a6999);
            color: #fff;
            font-family: arial;
            font-weight: bold;
            border-radius: 5px;
            padding: 10px 10px;
            display: table;
            margin: 50px auto;
        }
        .email-form-wrap .email-input,
        .email-form-wrap .submit-button {
            padding: 5px 10px;
        }
        .email-form-wrap .submit-button {
            background: linear-gradient(#f7941d, #d75305);
            box-shadow: inset 0px 1px 0px #ffbb6a, inset 0 -1px 2px #a33f03;
            border-radius: 5px;
            border: 1px solid #333;
            color: #fff;
            text-shadow: 1px 1px #521601;
            font-family: arial;
            font-weight: bold;
        }
        .email-form-wrap .email-input {
            background-color: #333;
            color: #fff;
            border: 1px solid #000;
            border-radius: 5px;
        }
        .bracket {
            font-size: 250px;
            color: #333;
        }
        .brackets-content {
          text-align: center;
          font-family: 'helvetica', 'arial', 'sans serif';
        }
        .brackets-content .bracket {
        }
        .brackets-content .content {
          text-align: center;
        }
        .brackets-content > span {
          float: left;
          width: 33.3%;
        }
        .clearfix:after {
          content: ".";
          display: block;
          clear: both;
          visibility: hidden;
          line-height: 0;
          height: 0;
        }
        .message {
            margin-top: 0;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="brackets-content clearfix">
      <span class="bracket left">{</span>
      <span class="content">
        <h2>Fique Ligado!</h2>
        <h3>Tem muito conteúdo bacana</h3>
            Você é um artesão de software? <br>
            Se interessa por novas tecnologias e tutorias de programação e boas práticas de código? Então deixe seu email e não perca nenhuma atualização.<br>
            Enquanto isso, acessa <a href="http://fb.com/ateliedocodigo" target="_blank">Facebook</a>, <a href="https://www.youtube.com/channel/UCJtkz7su6iT_jZva5RoSZiQ" target="_blank">Youtube</a>, <a href="http://twitter.com/ateliedocodigo" target="_blank">Twitter</a>
        </span>
      <span class="bracket right">}</span>
    </div>
    <div class="email-form-wrap">
        <form method="post">
<?php if (filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
    <?php
        try {
            $api->lists->subscribe($config['listId'], array('email'=> $email));
            echo '<p class="message">Confirma no teu email a inscrição por favor.</p>';
        } catch (Mailchimp_Invalid_Email $e) {
            echo 'Coloca um email válido por favor!';
            $api->log($e->getMessage());
        } catch (Mailchimp_List_AlreadySubscribed $e) {
            echo '<p class="message">Teu email já está cadastrado. Valeu!</p>';
            $api->log($e->getMessage());
        } catch (Mailchimp_Error $e) {
            echo '<p class="message">Coloca um email válido por favor!</p>';
            $api->log($e->getMessage());
        }
        ?>
<?php else: ?>
<?php endif;
?>
            <label>
                Email*
                <input type="email" required name="email" class="email-input" value="<?php echo $email; ?>">
            </label>
            <button class="submit-button" type="submit">Quero receber!</button>
        </form>
    </div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-84648519-1', 'auto');
  ga('send', 'pageview');
</script>
</body>
</html>
