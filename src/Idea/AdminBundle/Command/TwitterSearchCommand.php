<?php
namespace Idea\AdminBundle\Command;



use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TwitterSearchCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('idea:tweet:search')
            ->setDescription('fetch tweets by a given tag')
            ->addArgument(
                'tag',
                InputArgument::OPTIONAL,
                'tag to search (default = php)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tweets = array();
        $table = $this->getHelperSet()->get('table');
        $twitter = $this->getContainer()->get('twitter');

        $tag = ($input->getArgument('tag')?$input->getArgument('tag'):'php');
        $data = $twitter->findBy($tag);

        foreach($data['statuses'] as $status)
        {
            $tweets[] = array(
                $this->getTagsAsString($status),
//                $status['id'],
                $status['text'],
//                $status['user']['id'],
                $status['user']['name'],
//                $status['user']['screen_name'],
//                $status['user']['profile_image_url'],
//                $status['user']['location'],
//                $status['user']['description'],
//                $status['user']['created_at'],
                $status['created_at'],
            );
//            var_dump($status);die;
//            var_dump($status['user']);die;
//            var_dump($status['entities']['hashtags']);die;
        }

        $table
            ->setHeaders(array('tags', 'text', 'name', 'created_at'))
            ->setRows($tweets)
        ;
        $table->render($output);

    }

    private function getTagsAsString($status)
    {
        $ret = "";
        if (isset($status['entities']['hashtags']))
            foreach($status['entities']['hashtags'] as $elem)
            {
                $ret.= $elem['text']." ";
            }
        return $ret;
    }


}