<?php
namespace Elbasri\Gold\Model\Entity\Attribute\Source;
 
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;
 

class Openin extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $optionFactory;
 
    public function __construct(OptionFactory $optionFactory)
    {
        $this->optionFactory = $optionFactory;  
    }
 	public function getAllOptions()  {
        /* your Attribute options list*/
        $this->_options=[ ['label'=>'New Window', 'value'=>'_blank'],
                          ['label'=>'Same Window', 'value'=>'_self']
                         ];
        return $this->_options;
    }
}