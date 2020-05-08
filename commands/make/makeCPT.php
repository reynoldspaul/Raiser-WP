<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class makeCPT extends Command
{

    // example command: php raiser make:cpt post_type_name

    protected $commandName = 'make:cpt';
    protected $commandDescription = "Make a CPT";

    protected $commandArgumentPostTypeName = "post_type_name";
    protected $commandArgumentPostTypeNameDescription = "";

    protected $commandOptionTemplates = "templates"; // should be specified like "--templates"
    protected $commandOptionDescription = 'If set, will create templates';    

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentPostTypeName,
                InputArgument::OPTIONAL,
                $this->commandArgumentPostTypeNameDescription
            )
            ->addOption(
               $this->commandOptionTemplates,
               null,
               InputOption::VALUE_NONE,
               $this->commandOptionDescription
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        flush_rewrite_rules();

        //
        // get arguments
        $post_type_name = $input->getArgument($this->commandArgumentPostTypeName);

        // define file name
        $new_file = __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/cpts/'.sanitize_title($post_type_name).'.php';

        // check if cpt already exists
        if( file_exists($new_file) ){
            $output->writeln('CPT File already exists.');
        } else {

            $stub = file_get_contents(__DIR__.'/../stubs/cpt.stub');

            // replace things
            $stub = str_replace('DummyClass', str_replace(' ','_',ucwords(str_replace('-',' ',$post_type_name))).'_CPT', $stub);
            $stub = str_replace('upper_dummy_post_type_name', ucwords( str_replace('-', ' ', $post_type_name)), $stub);
            $stub = str_replace('dummy_post_type_name', strtolower($post_type_name), $stub);        

            
            // make the file 	
            if ( !is_dir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/' ) ) {
                mkdir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/' );  
            }             
            if ( !is_dir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/cpts/' ) ) {
                mkdir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/cpts/' );  
            }  
            file_put_contents($new_file, $stub);           
            $output->writeln('File Created: '.$new_file);

        }

        // template files

        $templates = $input->getOption('templates');
        if( $templates == null ){
            return;
        }

        $new_file = __WP_TEMPLATE_DIR__.'/single-'.sanitize_title($post_type_name).'.php';
        if( file_exists($new_file) ){
            $output->writeln($post_type_name .' Single Template File already exists.');
        } else {
            $stub = file_get_contents(__DIR__.'/../stubs/templates/single-cpt.stub');
            file_put_contents($new_file, $stub);
            $output->writeln('File Created: '.$new_file);

        }

        $new_file = __WP_TEMPLATE_DIR__.'/archive-'.sanitize_title($post_type_name).'.php';
        if( file_exists($new_file) ){
            $output->writeln($post_type_name .' Archive Template File already exists.');
        } else {
            $stub = file_get_contents(__DIR__.'/../stubs/templates/archive-cpt.stub');
            file_put_contents($new_file, $stub);
            $output->writeln('File Created: '.$new_file);

        }

    }
}