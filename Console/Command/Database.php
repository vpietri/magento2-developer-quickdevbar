<?php

namespace ADM\QuickDevBar\Console\Command;


use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProductAttributesCleanUp
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Database extends \Symfony\Component\Console\Command\Command
{
    const DDL_COMMENT_TABLE_KEY='CREATE_TABLENAME_COMMENT_KEY';

    const DDL_ENGINE_KEY='CREATE_TABLENAME_ENGINE_KEY';


    const DB_TABLENAME='table';

    const DB_TABLE_COMMENT='comment';

    /**
     * @var string
     */
    protected $name='dev:quickdevbar:dbschema';

    /**
     * @var string
     */
    protected $description='Retrieve db schema to fit Magento schema.xsd';

    private  $resource;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource,
                                string                $name = null)
    {
        parent::__construct($name);

        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->name);
        $this->setDescription($this->description);
        $this->addArgument(
            self::DB_TABLENAME,
            \Symfony\Component\Console\Input\InputArgument::REQUIRED,
            'Table name to describe'
        );
        $this->addOption(
            self::DB_TABLE_COMMENT,
            'c',
            null,
            'Table name comment'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $connection = $this->resource->getConnection();
        $tableName = $input->getArgument(self::DB_TABLENAME);

        $ddl = $connection->describeTable($input->getArgument(self::DB_TABLENAME));

        $createTable = $connection->getCreateTable($input->getArgument(self::DB_TABLENAME));
        $createTableComment = [self::DDL_COMMENT_TABLE_KEY => $tableName . ' Table'];

        if(preg_match_all('/.*COMMENT[\s=]\'(.*)\'/', $createTable, $matchComment)) {

        }

        $tableEngine = 'innodb';
        $tableComment = $tableName . ' Table';

        $dbSchemaArr = ['<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd">'];
        $dbSchemaArr[] = '    <table name="'.$tableName.'" resource="default" engine="'.$tableEngine.'" comment="'.$tableComment.'">';

        foreach ($ddl as $ddlColumn) {
            $xmlColumn=[];
            $xmlColumn['name'] = $ddlColumn['COLUMN_NAME'];
            $xmlColumn['xsi:type'] = $ddlColumn['DATA_TYPE'];
            $xmlColumn['nullable'] = $ddlColumn['NULLABLE'];
            if($ddlColumn['DEFAULT']) {
                $xmlColumn['default'] = $ddlColumn['DEFAULT'];
            }
            $xmlColumn['scale'] = $ddlColumn['SCALE'];
            $xmlColumn['precision'] = $ddlColumn['PRECISION'];
            $xmlColumn['unsigned'] = $ddlColumn['UNSIGNED'];
            if($ddlColumn['PRIMARY']) {
                $xmlColumn['primary'] = $ddlColumn['PRIMARY'];
            }
            if($ddlColumn['IDENTITY']) {
                $xmlColumn['identity'] = $ddlColumn['IDENTITY'];
            }
            $xmlColumn['length'] = $ddlColumn['LENGTH'];
            $xmlColumn['comment'] = $ddlColumn['COLUMN_NAME'];
            array_filter($xmlColumn, fn($var) => $var !== null);
            array_walk($xmlColumn, function(&$item, $key) {
                if(!is_string($item)) {
                    $item = empty($item) ? 'false' : 'true';
                }
                $item = $key . '="'.$item.'"';
            });

            $dbSchemaArr[] = '        <column ' . implode(' ', $xmlColumn) . '/>';
        }
        $dbSchemaArr[] = '    </table>';
        $dbSchemaArr[] = '</schema>';


        $output->writeln("<info>" . implode(PHP_EOL, $dbSchemaArr) . "</info>");

        return Cli::RETURN_SUCCESS;
    }

}
