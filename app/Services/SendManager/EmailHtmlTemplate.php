<?php

namespace App\Services\SendManager;

class EmailHtmlTemplate
{
    public static function layout($content)
    {
        return '<!DOCTYPE html>
              <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="X-UA-Compatible" content="ie=edge">
                    <title>' . $_ENV['APP_NAME'] . '</title>
                </head>
                <body>
                  <div style=\'background: #FAFAFA; padding: 5rem 0; text-align: center;\'>
                    <div style=\'max-width:590px!important; width:590px; background: white;padding: 1rem;margin: auto;\'>
                       ' . $content . '
                    </div>
                    <div style="text-align: center; color: #888888; margin-top: 1rem;">
                       Copyright ©' . date('Y') . ' ' . $_ENV['APP_AUTHOR'] . '
                    </div>
                  </div>
                </body>
              </html>
        ';
    }

    public static function invoice(){
        return self::layout('
          <table style="width: 100%">
              <tbody>
                  <tr>
                      <td>
                          <table style="margin-right: auto; margin-left: auto; width: 100%; max-width: 600px; background-color: #ffffff">
                              <tbody>
                                  <tr>
                                      <td style="background: #007BE8; color: #FFFFFF; padding: 32px 16px 28px 16px">
                                          <div>{{documentDescription}}</div>
                                          <div style="font-weight: bold; font-size: 64px">{{serie}}-{{number}}</div>
                                      </td>
                                  </tr>
                                  <tr>
                                      <td style="height: 16px"></td>
                                  </tr>
                                  <tr>
                                      <td style="font-size: 32px">{{socialReason}}</td>
                                  </tr>
                                  <tr>
                                      <td>Se adjunta en este mensaje una {{documentDescription}}</td>
                                  </tr>
                                  <tr>
                                      <td>
                                          <ul>
                                              <li>{{documentDescription}} {{serie}}-{{number}}</li>
                                              <li>Fecha de emisión: {{dateOfIssue}}</li>
                                              <li>Fecha de vencimiento: {{dateOfDue}}</li>
                                              <li>Total: {{total}}</li>
                                          </ul>
                                      </td>
                                  </tr>
                                  <tr><td>También puedes ver el documento visitando el siguiente link.</tr>
                                  <tr>
                                      <td style="height: 16px"></td>
                                  </tr>
                                  <tr>
                                      <td>
                                          <a href="{{documentUrl}}" style="text-decoration: none; font-size: 14px; background: #007BE8; display: inline-block; padding: 0 16px; line-height: 42px; height: 42px; border-radius: 6px; color: white;">VER {{documentDescription}}</a>
                                      </td>
                                  </tr>
                                  <tr>
                                      <td style="height: 16px"></td>
                                  </tr>
                                  <tr><td>Si el link no funciona, usa el siguiente enlace en tu navegador:</td></tr>
                                  <tr>
                                      <td><a href="{{documentUrl}}">{{documentUrl}}</a></td>
                                  </tr>
                              </tbody>
                          </table>
                      </td>
                  </tr>
              </tbody>
          </table>');
      }
}
