<?php

namespace App\Services\SendManager;

use App\Http\Models\Result;
use Exception;

class EmailManager
{
    public static function send($from, $to, $subject, $message)
    {
        $res = new Result();
        try {
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email del destinataio es invalido');
            }
            if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email de origen es invalido');
            }

            $headers = "From: " . $_ENV['APP_NAME'] . " <{$from}>" . "\r\n";
            // $headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
            // $headers .= "CC: susan@example.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            // HTML MESSAGE START
            $message = EmailHtmlTemplate::layout($message);
            $res->success = mail($to, $subject, $message, $headers);
        } catch (Exception $e) {
            $res->success = false;
            $res->message = $e->getMessage();
        }
        return $res;
    }

    public static function sendInvoice($to, $subject, $from, $senderName, $document, $files = array())
    {
      $res = new Result();
      try {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)){
          throw new Exception('Email del destinataio es invalido');
        }
        if (!filter_var($from, FILTER_VALIDATE_EMAIL)){
          throw new Exception('Email de origen es invalido');
        }

        $invoiceTemplate = EmailHtmlTemplate::invoice();
        foreach($document as $key => $value)
        {
            $invoiceTemplate = str_replace('{{'.$key.'}}', $value, $invoiceTemplate);
        }

        // a random hash will be necessary to send mixed content
        $semiRand = md5(time());
        $separator = "==Multipart_Boundary_x{$semiRand}x";

        $eol = "\r\n"; // carriage return type (RFC)

        // main header (multipart mandatory)
        $headers = "From: {$senderName} <{$from}>" . $eol;
        $headers .= "MIME-Version: 1.0" . $eol;
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$separator}\"" . $eol;
        $headers .= "Content-Transfer-Encoding: 7bit" . $eol;

        // message
        $body = "--{$separator}" . $eol;
        $body .= "Content-Type: text/html; charset=\"UTF-8\"" . $eol;
        $body .= "Content-Transfer-Encoding: 7bit" . $eol;
        $body .= $invoiceTemplate . $eol;

        // attachment
        if (!empty($files)) {
            for ($i = 0; $i < count($files); $i++) {
                if (is_file($files[$i])) {
                    $fileName = basename($files[$i]);
                    $fileSize = filesize($files[$i]);
                    $fileType = mime_content_type($files[$i]);

                    $fileStream = fopen($files[$i], "rb");
                    $fileContent = fread($fileStream, $fileSize);
                    fclose($fileStream);
                    $fileContentEncoded = chunk_split(base64_encode($fileContent));

                    $body .= "--" . $separator . $eol;
                    $body .= "Content-Type: $fileType; name=\"" . $fileName . "\"" . $eol;
                    $body .= "Content-Transfer-Encoding: base64" . $eol;
                    $body .= "Content-Disposition: attachment; filename=\"" . $fileName . "\"" . $eol;
                    $body .= "X-Attachment-Id: " . rand(1000, 99999) . $eol . $eol;
                    $body .= $fileContentEncoded . $eol;
                }
            }
        }

        // $body .= "--{$separator}--";
        // $returnpath = "-f" . $senderEmail;

        $res->success = mail($to, $subject, $body, $headers);
      } catch (Exception $e){
        $res->success = false;
        $res->message = $e->getMessage();
      }
      return $res;
    }
}
