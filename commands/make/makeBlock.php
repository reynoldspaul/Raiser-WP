<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class makeBlock extends Command
{

    // example command: php raiser make:block block_name --type=acf

    protected $commandName = 'make:block';
    protected $commandDescription = "Make a Block";

    protected $commandArgumentBlockName = "block_name";
    protected $commandArgumentBlockNameDescription = "";

    protected $commandOptionType = "type"; 
    protected $commandOptionDescription = 'Set the type of block, acf or gacf';    

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentBlockName,
                InputArgument::OPTIONAL,
                $this->commandArgumentBlockNameDescription
            )
            ->addOption(
               $this->commandOptionType,
               null,
               InputArgument::OPTIONAL,
               $this->commandOptionDescription,
               'acf'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        flush_rewrite_rules();
                
        //
        // get arguments
        $block_name = $input->getArgument($this->commandArgumentBlockName);

        $block_type = $input->getOption($this->commandOptionType);

        if( !in_array($block_type, ['acf','gacf'] ) ){
            $output->writeln('Block type '.$block_type.' not supported. Only acf or gacf.');
            return;
        }

        if ( !is_dir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/' ) ) {
            mkdir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/' );  
        }           

        // define file name
        if ( 'gacf' == $block_type) {
            $new_file = __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/blocks/'.sanitize_title($block_name).'/'.sanitize_title($block_name).'.php';
        } else {
            $new_file = __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/blocks/'.sanitize_title($block_name).'.php';
        }

        // check if cpt already exists
        if( file_exists($new_file) ){
            $output->writeln('File already exists.');
            return;
        }

        $stub = file_get_contents(__DIR__.'/../stubs/'.$block_type.'_block.stub');

        // replace things
        $stub = str_replace('DummyClass', str_replace(' ','_',ucwords(str_replace('-',' ',$block_name))).'_Block', $stub);
        $stub = str_replace('upper_dummy_block_name', ucwords( str_replace('-', ' ', $block_name)), $stub);
        $stub = str_replace('dummy_block_name', strtolower($block_name), $stub);        


        # ANDY NEW TEMPLATE STUB
        if ( 'gacf' == $block_type) {
            // define file name
            $new_template_file = __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/blocks/'.sanitize_title($block_name).'/template.php';

            // check if cpt already exists
            if( file_exists($new_template_file) ){
                $output->writeln('File already exists.');
                return;
            }

            $template_stub = file_get_contents(__DIR__.'/../stubs/'.$block_type.'_template.stub');

            // replace things
            $template_stub = str_replace('upper_dummy_block_name', ucwords( str_replace('-', ' ', $block_name)), $template_stub);
            $template_stub = str_replace('dummy_block_name', strtolower( str_replace('-', '_', $block_name)), $template_stub);        
        }
        # END ANDY


        
        // make the file
        if ( !is_dir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/blocks/' ) ) {
            mkdir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/blocks/' );  
        }
        if ( 'gacf' == $block_type) {
            if ( !is_dir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/blocks/'.sanitize_title($block_name).'/' ) ) {
                mkdir( __WP_TEMPLATE_DIR__.'/'.Raiser_WP::THEME_CONTENT_FOLDER.'/blocks/'.sanitize_title($block_name).'/' );  
            } 
        }

    	file_put_contents($new_file, $stub);
        $output->writeln('File Created: '.$new_file);

        # ANDY
        if ( 'gacf' == $block_type) {
            file_put_contents($new_template_file, $template_stub);
            $output->writeln('File Created: '.$new_template_file);
        }
        # END ANDY

    }
}