<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class initConfig extends Command
{

    // example command: php raiser init:config --configs=admin --configs=theme --theme_dir=dir/path

    protected $commandName = 'init:config';
    protected $commandDescription = "Init config files in the theme";

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addOption(
                'configs',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Specefic Config files'
            )    
            ->addOption(
               'theme_dir',
               null,
               InputOption::VALUE_NONE,
               'Theme directory'
            )                     
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $files = $input->getOption('configs');

        $__WP_TEMPLATE_DIR__ = __WP_TEMPLATE_DIR__;

        $theme_dir = $input->getOption('theme_dir');
        if( $theme_dir != null ){  
            $__WP_TEMPLATE_DIR__ = $theme_dir;
        }        

        if ( !is_dir( $__WP_TEMPLATE_DIR__.'/'.Raiser_Config::THEME_CONFIG_FOLDER ) ) {
            mkdir( $__WP_TEMPLATE_DIR__.'/'.Raiser_Config::THEME_CONFIG_FOLDER );  
        }    

        if( empty($files) || in_array('admin', $files) ){    

            // define file name
            $new_file = $__WP_TEMPLATE_DIR__.'/'.Raiser_Config::THEME_CONFIG_FOLDER.'/admin.php';

            // check if already exists
            if( file_exists($new_file) ){
                $output->writeln('Admin Config File already exists.');
            } else {

                $stub = file_get_contents(__DIR__.'/../stubs/config/admin.stub');
                // make the file
            	file_put_contents($new_file, $stub);
                $output->writeln('Admin Config Created: '.$new_file);
            }

        }

        if( empty($files) || in_array('theme', $files) ){ 

            $new_file = $__WP_TEMPLATE_DIR__.'/'.Raiser_Config::THEME_CONFIG_FOLDER.'/theme.php';

            // check if already exists
            if( file_exists($new_file) ){
                $output->writeln('Theme Config File already exists.');
            } else {

                $stub = file_get_contents(__DIR__.'/../stubs/config/theme.stub');
                // make the file
                file_put_contents($new_file, $stub);
                $output->writeln('Theme Config Created: '.$new_file);
            }

        }

        if( empty($files) || in_array('gutenberg', $files) ){ 

            $new_file = $__WP_TEMPLATE_DIR__.'/'.Raiser_Config::THEME_CONFIG_FOLDER.'/gutenberg.php';

            // check if already exists
            if( file_exists($new_file) ){
                $output->writeln('Gutenberg Config File already exists.');
            } else {

                $stub = file_get_contents(__DIR__.'/../stubs/config/gutenberg.stub');
                // make the file
                file_put_contents($new_file, $stub);
                $output->writeln('Gutenberg Config Created: '.$new_file);
            }    

        }    

    }
}