<?php
namespace Elbasri\Zaincash\Helper;
class Data extends \Magento\Payment\Helper\Data
{
    protected $_code;

    public function setMethodCode($code) {
        $this->_code = $code;
    }
}