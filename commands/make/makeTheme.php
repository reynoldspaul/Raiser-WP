<?php
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class makeTheme extends Command
{

    // example command: php raiser make:theme theme_name  --npm

    protected $commandName = 'make:theme';
    protected $commandDescription = "Make a Theme";

    protected $commandArgumentThemeName = "theme_name";
    protected $commandArgumentThemeDescription = "";

    protected $commandOptionNpm = "npm"; // should be specified like "--templates"
    protected $commandOptionDescription = 'If set, will create sass and js compiler';    

    protected function configure()
    {
        $this
            ->setName($this->commandName)
            ->setDescription($this->commandDescription)
            ->addArgument(
                $this->commandArgumentThemeName,
                InputArgument::OPTIONAL,
                $this->commandArgumentThemeDescription
            )
            ->addOption(
               $this->commandOptionNpm,
               null,
               InputOption::VALUE_NONE,
               $this->commandOptionDescription
            )
            ->addOption(
               'config',
               null,
               InputOption::VALUE_NONE,
               'Sets up config files'
            )            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //
        // get arguments
        $theme_name = $input->getArgument($this->commandArgumentThemeName);
        $theme_slug = sanitize_title($theme_name);

        $theme_dir_path = get_theme_root().'/'. $theme_slug. '/';



        // check if cpt already exists
        if( file_exists($theme_dir_path) ){
            $output->writeln('Theme already exists.');
            return;
        }

        // make dir
        mkdir( $theme_dir_path );

        // make theme
        $this->recurse_copy( __DIR__.'/../stubs/theme/theme', $theme_dir_path);

        // npm 
        $npm = $input->getOption('npm');
        if( $npm != null ){
           $this->recurse_copy( __DIR__.'/../stubs/theme/npm', $theme_dir_path); 
        }

        // renam vars
        $this->recursive_rename( $theme_dir_path, [
            'theme_name' => $theme_name,
            'theme_slug' => $theme_slug
        ]);   

        // config 
        $config = $input->getOption('config');
        if( $config != null ){  
            $arguments = [
                '--theme_dir' => $theme_dir_path,
            ];            
            $command = $this->getApplication()->find('init:config');
            $command->run(new ArrayInput($arguments), new NullOutput);

            // lets command to create files finish
            usleep(500);

            $this->recursive_rename( $theme_dir_path.Raiser_Config::THEME_CONFIG_FOLDER.'/', [
                'my-custom-theme' => $theme_slug
            ]);  
        }         

        $output->writeln('Theme Created: '.$theme_dir_path);

    }

    protected function recurse_copy($src,$dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    $new_file = str_replace('.stub', '', $file);
                    copy($src . '/' . $file,$dst . '/' . $new_file); 
                } 
            } 
        } 
        closedir($dir); 
    } 

    protected function recursive_rename($src, $renames){

        $files = glob($src.'*');
        foreach($files as $file) {
            if(is_dir( $file )){
                $this->recursive_rename($file.'/', $renames);
            } else {
                $stub = file_get_contents($file);
                // do replace
                foreach( $renames as $find=>$repalce ){
                    $stub = str_replace($find, $repalce, $stub);  
                }
                file_put_contents($file, $stub); 
            }
        }

    }

}