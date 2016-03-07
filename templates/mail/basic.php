<?php

$email_template = '
<html>
<head>
<meta name="viewport" content="width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Backup App - System Log</title>
<style>
/* -------------------------------------
    GLOBAL
------------------------------------- */
* {
  font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
  font-size: 100%;
  line-height: 1.6em;
  margin: 0;
  padding: 0;
}

img {
  max-width: 600px;
  width: 100%;
}

body {
  -webkit-font-smoothing: antialiased;
  height: 100%;
  -webkit-text-size-adjust: none;
  width: 100% !important;
}


/* -------------------------------------
    ELEMENTS
------------------------------------- */
a {
  color: #348eda;
}

.btn-primary {
  Margin-bottom: 10px;
  width: auto !important;
}

.btn-primary td {
  background-color: #348eda;
  border-radius: 25px;
  font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
  font-size: 14px;
  text-align: center;
  vertical-align: top;
}

.btn-primary td a {
  background-color: #348eda;
  border: solid 1px #348eda;
  border-radius: 25px;
  border-width: 10px 20px;
  display: inline-block;
  color: #ffffff;
  cursor: pointer;
  font-weight: bold;
  line-height: 2;
  text-decoration: none;
}

.last {
  margin-bottom: 0;
}

.first {
  margin-top: 0;
}

.padding {
  padding: 10px 0;
}


/* -------------------------------------
    BODY
------------------------------------- */
table.body-wrap {
  padding: 20px;
  width: 100%;
}

table.body-wrap .container {
  border: 1px solid #f0f0f0;
}


/* -------------------------------------
    FOOTER
------------------------------------- */
table.footer-wrap {
  clear: both !important;
  width: 100%;
}

.footer-wrap .container p {
  color: #666666;
  font-size: 12px;

}

table.footer-wrap a {
  color: #999999;
}


/* -------------------------------------
    TYPOGRAPHY
------------------------------------- */
h1,
h2,
h3 {
  color: #111111;
  font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
  font-weight: 200;
  line-height: 1.2em;
  margin: 40px 0 10px;
}

h1 {
  font-size: 36px;
}
h2 {
  font-size: 28px;
}
h3 {
  font-size: 22px;
}

p,
ul,
ol {
  font-size: 14px;
  font-weight: normal;
  margin-bottom: 10px;
}

ul li,
ol li {
  margin-left: 5px;
  list-style-position: inside;
}

/* ---------------------------------------------------
    RESPONSIVENESS
------------------------------------------------------ */

/* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
.container {
  clear: both !important;
  display: block !important;
  Margin: 0 auto !important;
  max-width: 600px !important;
}

/* Set the padding on the td rather than the div for Outlook compatibility */
.body-wrap .container {
  padding: 20px;
}

/* This should also be a block element, so that it will fill 100% of the .container */
.content {
  display: block;
  margin: 0 auto;
  max-width: 600px;
}

/* Let\'s make sure tables in the content area are 100% wide */
.content table {
  width: 100%;
}

</style>
<style id="style-1-cropbar-clipper">/* Copyright 2014 Evernote Corporation. All rights reserved. */
.en-markup-crop-options {
    top: 18px !important;
    left: 50% !important;
    margin-left: -100px !important;
    width: 200px !important;
    border: 2px rgba(255,255,255,.38) solid !important;
    border-radius: 4px !important;
}

.en-markup-crop-options div div:first-of-type {
    margin-left: 0px !important;
}
</style></head>

<body bgcolor="#f6f6f6">

<!-- body -->
<table class="body-wrap" bgcolor="#f6f6f6">
  <tbody><tr>
    <td></td>
    <td class="container" bgcolor="#FFFFFF">

      <!-- content -->
      <div class="content">
      <table>
        <tbody><tr>
          <td>
            <p>Hola '.$mail_name.',</p> ';

            if($mail_mode == 'cron'){
                $email_template += '<p>Queríamos informarte que la backup programada para el '.$this->email_info['day'].' a las '.$this->email_info['time'].' se ha realizado correctamente.</p>';
            }else{
                $email_template += '<p>Queríamos informarte que la backup programada se correctemente.</p>';
            }

            $email_template .='
            <h1>'.$this->email_info['name_file'].'</h1>
            <p>En esta copia de seguridad se ha incluido:</p>
            <p></p>
            <h2>Resumen</h2>';

            if($this->email_info['type']== 'folder'){
                $email_template .='
                   <p>Directorio: '.$this->email_info['folder'].'</p>';
            }else{
                $email_template .='
                <p>Base de datos: '.$this->email_info['folder'].'</p>';
            }
            if(!empty($this->email_info['database'])){
                $email_template .='
                <p>Base de datos: '.$this->email_info['database'].'</p>';
            }

            $email_template .='
            <!-- button -->
            <table class="btn-primary" cellpadding="0" cellspacing="0" border="0">
              <tbody><tr>
                <td>
                  <a href="https://github.com/leemunroe/html-email-template">View the source and instructions on GitHub</a>
                </td>
              </tr>
            </tbody></table>
            <!-- /button -->
            <p>Este email es meramente informativo, no es necesario que responda.</p>
            <p>Para cualquier cuestión póngase en contacto con el administrador.</p>
            <p>Gracias, le deseamos que tenga un buen día.</p>
          </td>
        </tr>
      </tbody></table>
      </div>
      <!-- /content -->
        <table class="footer-wrap">
          <tbody><tr>
            <td></td>
            <td class="container">

              <!-- content -->
              <div class="content">
                <table>
                  <tbody><tr>
                    <td align="center">
                      <p>Si no desea recibir este correo, cambie la configuración en el archivo "action.php" de Backup APP.
                      </p>
                    </td>
                  </tr>
                </tbody></table>
              </div>
              <!-- /content -->

            </td>
            <td></td>
          </tr>
        </tbody></table>
    </td>
    <td></td>
  </tr>
</tbody></table>
<!-- /body -->

</body></html>


';

