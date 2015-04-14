<?php
namespace Acme\SocialBundle\Command;



use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TwitterImportCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('acme:social:tweet:import')
            ->setDescription('fetch tweets by a given tag and import')
            ->addArgument(
                'tag',
                InputArgument::OPTIONAL,
                'tag to search (default = php)'
            )
            ->addOption(
                'auto-approve',
                'auto',
                InputOption::VALUE_NONE,
                'auto approve twitter content '
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = $this->getHelperSet()->get('table');

        $twitter = $this->getContainer()->get('twitter');
        $manager = $this->getContainer()->get('twitter.manager');

        if ($input->getOption('auto-approve')) {
            $manager->autoApprove();
        }

        $tag = ($input->getArgument('tag')?$input->getArgument('tag'):'php');
        $data = $twitter->findBy($tag);

        // some stats...
        $skipped=0;
        $insert=0;
        $exist=0;

        foreach($data['statuses'] as $status)
        {

            try {
                $result = $manager->process($status);
                if($result)
                    $insert++;
                else
                    $exist++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        $table
            ->setHeaders(array('stat','count'))
            ->setRows(
                array(
                    array('insert', $insert),
                    array('exist', $exist),
                    array('skipped',$skipped)
                )
            );
        ;
        $table->render($output);

    }

}