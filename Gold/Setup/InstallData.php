<?php
namespace Elbasri\Gold\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
	
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
	
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        //$setup->startSetup();
  		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'allowproductview',
            [
                'group' => 'Gold Product Grams',
        		'label' => 'Enable Gold Product',
				'type'  => 'int',
        		'input' => 'boolean',
        		'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'source' => '',
                'required' => false,
                'sort_order' => 1,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
				'apply_to' => 'elbasri_gold'
            ]
        );
		
		$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'gramme',
            [
                'group' => 'Gold Product Grams',
        		'label' => 'Gram QTY',
				'type'  => 'varchar',
        		'input' => 'text',
                'source' => '',
                'required' => false,
                'sort_order' => 2,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
				'apply_to' => 'elbasri_gold',
				'note' => 'عدد الغرامات في المنتج الواحد'
            ]
        );
		
		/*$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'linetext',
            [
                'group' => 'Gold Product Link',
        		'label' => 'Link Text',
				'type'  => 'varchar',
        		'input' => 'text',
                'source' => '',
                'required' => false,
                'sort_order' => 2,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
				'apply_to' => 'elbasri_gold'
            ]
        );
		$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'openin',
            [
                'group' => 'Gold Product Link',
        		'label' => 'Open In',
				'type'  => 'varchar',
        		'input' => 'select',
                'source' => 'Elbasri\Gold\Model\Entity\Attribute\Source\Openin',
                'required' => false,
                'sort_order' => 3,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
				'apply_to' => 'elbasri_gold'
            ]
        );*/
		// Price Attribute set in Gold Product Type
		  $fieldList = [
			  'price',
			  'special_price',
			  'special_from_date',
			  'special_to_date',
			  'minimal_price',
			  'cost',
			  'tier_price',
		  ];
		  foreach ($fieldList as $field) {
			  $applyTo = explode(
				  ',',
				  $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $field, 'apply_to')
			  );
			  if (!in_array('elbasri_gold', $applyTo)) {
				  $applyTo[] = 'elbasri_gold';
				  $eavSetup->updateAttribute(
					  \Magento\Catalog\Model\Product::ENTITY,
					  $field,
					  'apply_to',
					  implode(',', $applyTo)
				  );
			  }
		  }
		$setup->endSetup();
    }
}