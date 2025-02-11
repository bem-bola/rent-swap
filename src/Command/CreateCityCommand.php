<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use App\Service\HttpClientService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:create:city')]
class CreateCityCommand extends Command
{
    private HttpClientService $httpClientService;

    /**
     * @param HttpClientService $httpClientService
     */
    public function __construct(HttpClientService $httpClientService)
    {
        $this->httpClientService = $httpClientService;
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {


       $departements = [
            // France métropolitaine
//            "01", "02", "03", "04", "05", "06", "07", "08", "09", "10",
//            "11", "12", "13", "14", "15", "16", "17", "18", "19", "2A", "2B",

//            "21", "22", "23", "24", "25", "26", "27", "28", "29", "30",
//            "31", "32", "33", "34", "35", "36", "37", "38", "39", "40",
//            "41", "42", "43", "44", "45", "46", "47", "48", "49", "50",
//
//
//            "51", "52", "53", "54", "55", "56", "57", "58", "59", "60",
//            "61", "62", "63", "64", "65", "66", "67", "68", "69", "70",
//            "71", "72", "73", "74", "75", "76", "77", "78", "79", "80",
//            "81", "82", "83", "84", "85", "86", "87", "88", "89", "90",
//            "91", "92", "93", "94",
           "95",
//
            // Départements d'outre-mer (DOM)
//            "971", "972", "973", "974", "976",
//
//            // Collectivités d'outre-mer et autres
//            "977", "978", "984", "986", "987", "988"
        ];
        foreach ($departements as $city) {
            $this->httpClientService->getCities($city);
        }

        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}