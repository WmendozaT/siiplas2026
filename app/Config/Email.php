<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = 'siiplas.dnplanificacion@gmail.com';
    public string $fromName   = 'Sistema de Planificacion POA Web Cns';
    public string $recipients = '';

    public string $userAgent = 'CodeIgniter';
    public string $protocol  = 'smtp';
    public string $mailPath  = '/usr/sbin/sendmail';

    // AJUSTE: Agregamos ssl:// para forzar conexión segura desde el inicio
    public string $SMTPHost = 'smtp.gmail.com';
    public string $SMTPUser = 'siiplas.dnplanificacion@gmail.com';
    public string $SMTPPass = 'fmmgmikcadgrncsk';
    public int $SMTPPort    = 465;
    public string $SMTPCrypto = 'ssl';

    // ESTO ES LO QUE EVITA EL BLOQUEO DE AVAST
    // Permite que la conexión siga adelante aunque el antivirus intercepte el certificado
    public array $SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];

    public int $SMTPTimeout      = 5;
    public bool $SMTPKeepAlive   = false;
    public bool $wordWrap        = true;
    public int $wrapChars        = 76;
    public string $mailType      = 'html';
    public string $charset       = 'UTF-8';
    public bool $validate        = false;
    public int $priority         = 3;
    public string $CRLF          = "\r\n";
    public string $newline       = "\r\n";
    public bool $BCCBatchMode    = false;
    public int $BCCBatchSize     = 200;
    public bool $DSN             = false;
}