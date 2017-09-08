<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use App\Conversations\RegistrationConversation;
use BotMan\BotMan\Middleware\ApiAi;

class BotManController extends Controller
{


    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        // create an instance
        $botman = app('botman');    
        $apiAi = ApiAi::create('9d85bda2db97488eab4730b11bf48592')->listenForAction();

        // Apply global "received" middleware
        $botman->middleware->received($apiAi);

        // Apply matching middleware per hears command
        $botman->hears('.*', function (BotMan $bot) {
            // The incoming message matched the "my_api_action" on API.ai
            // Retrieve API.ai information:
            $extras = $bot->getMessage()->getExtras();
            $apiReply = $extras['apiReply'];
            $apiAction = $extras['apiAction'];
            $apiIntent = $extras['apiIntent'];
            
            $bot->reply($apiReply);
        })->middleware($apiAi);
        
        $botman->fallback(function($bot) {
            $bot->reply('Yuo seriously ruined the conversation. I didn\'t get that at all');
        });
        
        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }
}
