<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Twilio\Rest\Client;

class ClockSmsCommand extends Command {
	protected function configure() {
		$this
			// the name of the command (the part after "bin/console")
			->setName( 'app:clock-sms' )
			// the short description shown while running "php bin/console list"
			->setDescription( 'Sends the time by sms.' )
			->addArgument( 'phonenumber', InputArgument::REQUIRED, 'Target phonenumber' )
			->addOption( 'force-send', 'f', InputOption::VALUE_OPTIONAL, 'force send the message even if the condition is not met.', false )
			// the full command description shown when running the command with
			// the "--help" option
			->setHelp( 'This command allows you to sms the current time by sms...' );

		// ex : php bin\console app:clock-sms +33608310857
		// ex : php bin\console app:clock-sms +33608310857 --force-send=false
		// ex : php bin\console app:clock-sms +33608310857 --force-send=true
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		// Your Account SID and Auth Token from twilio.com/console
		$sid   = getenv( 'TWILIO_SID' );
		$token = getenv( 'TWILIO_TOKEN' );

		$client = new Client( $sid, $token );

		$now    = new \DateTime();
		$hour   = $now->format( 'H' );
		$minute = $now->format( 'i' );

		if ( $input->getOption( 'force-send' ) === 'true' || $hour === $minute ) {
			$message = "$hour h $minute";

			// Use the client to do fun stuff like send text messages!
			$client->messages->create(
			// the number you'd like to send the message to
				$input->getArgument( 'phonenumber' ),
				array(
					// A Twilio phone number you purchased at twilio.com/console
					'from' => getenv( 'TWILIO_FROM_TEL' ),
					// the body of the text message you'd like to send
					'body' => $message
				)
			);
			$output->writeln( "Sms Envoyé : $message" );
		}
	}
}