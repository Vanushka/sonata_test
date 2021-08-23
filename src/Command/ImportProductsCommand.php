<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Controller\ImportProductsController;

class ImportProductsCommand extends Command
{
    /**
     * @var ImportProductsController
     */
    private $importProducts;

    public function __construct(ImportProductsController $importProducts)
    {
        $this->importProducts = $importProducts;

        parent::__construct();
    }

    protected static $defaultName = 'app:import-products';

    protected function configure(): void
    {
        $this
            ->setDescription('Import products')
            ->setHelp('Эта команда позволяет вам импортировать товары')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Products Importer',
            '=================',
            '',
        ]);

        $this->importProducts->index();
    
        $output->writeln([
            'Success',
            '=================',
        ]);
    }
}