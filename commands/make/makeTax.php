<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class makeTax extends Command
{

    // example command: php raiser make:tax tax_name --object_types=name --object_types=another

    protected $commandName = 'make:tax';
    protected $commandDescription = "Make a Taxonpomy";

    protected $commandArgumentTaxName = "tax_name";
    protected $commandArgumentTaxDescription = "";    

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentTaxName,
                InputArgument::OPTIONAL,
                $this->commandArgumentTaxDescription
            )
            ->addOption(
                'object_types',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Object types to attached to taxonomy'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        flush_rewrite_rules();
        
        //
        // get arguments
        $tax_name = $input->getArgument($this->commandArgumentTaxName);

        // define file name
        $new_file = __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/tax/'.sanitize_title($tax_name).'.php';

        // check if cpt already exists
        if( file_exists($new_file) ){
            $output->writeln('File already exists.');
            return;
        }

        $stub = file_get_contents(__DIR__.'/../stubs/tax.stub');

        // replace things
        $stub = str_replace('DummyClass', str_replace(' ','_',ucwords(str_replace('-',' ',$tax_name))).'_Tax', $stub);
        $stub = str_replace('upper_dummy_tax_name', ucwords(str_replace('-', ' ', $tax_name)), $stub);
        $stub = str_replace('dummy_tax_name', strtolower(str_replace(' ', '-', $tax_name)), $stub);

        // object types
        $object_types = $input->getOption('object_types');
        if( $object_types ){
            $csv = '';
            foreach( $object_types as $object_type ){
                $csv .= "'".$object_type."',";
            }
            $csv = trim($csv, ',');

            $stub = str_replace('$object_types = [];', '$object_types = ['.$csv.'];', $stub);
        }
        
        // make the file
        if ( !is_dir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/' ) ) {
            mkdir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/' );  
        }           
        if ( !is_dir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/tax/' ) ) {
            mkdir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/tax/' );  
        }           
    	file_put_contents($new_file, $stub);

        $output->writeln('File Created: '.$new_file);
    }
}