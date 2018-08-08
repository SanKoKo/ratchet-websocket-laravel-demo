<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Http\Controllers\WebSocketController;

class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $loop   = React\EventLoop\Factory::create();
        $webSock = new React\Socket\SecureServer(
            new React\Socket\Server('0.0.0.0:8091', $loop),
            $loop,
            array(
                'local_cert'        => 'C:/xampp/apache/conf/ssl.crt/server.crt', // path to your cert
                'local_pk'          => 'C:/xampp/apache/conf/ssl.key/server.key', // path to your server private key
                'allow_self_signed' => TRUE, // Allow self signed certs (should be false in production)
                'verify_peer' => FALSE
            )
        );

        // Ratchet magic
        $webServer = new Ratchet\Server\IoServer(
            new Ratchet\Http\HttpServer(
                new Ratchet\WebSocket\WsServer(
                    new WebSocketController()
                )
            ),
            $webSock
        );

        $loop->run();
    }
}