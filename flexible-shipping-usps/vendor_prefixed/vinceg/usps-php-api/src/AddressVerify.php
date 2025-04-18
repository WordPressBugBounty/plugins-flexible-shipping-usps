<?php

namespace FlexibleShippingUspsVendor\USPS;

/**
 * USPS Address Verify Class
 * used to verify an address is valid.
 *
 * @since  1.0
 *
 * @author Vincent Gabriel
 */
class AddressVerify extends USPSBase
{
    /**
     * @var string - the api version used for this type of call
     */
    protected $apiVersion = 'Verify';
    /**
     * @var string - revision version for including additional response fields
     */
    protected $revision = '';
    /**
     * @var array - list of all addresses added so far
     */
    protected $addresses = [];
    /**
     * Perform the API call to verify the address.
     *
     * @return string
     */
    public function verify()
    {
        return $this->doRequest();
    }
    /**
     * returns array of all addresses added so far.
     *
     * @return array
     */
    public function getPostFields()
    {
        $postFields = !empty($this->revision) ? ['Revision' => $this->revision] : [];
        return array_merge($postFields, $this->addresses);
    }
    /**
     * Add Address to the stack.
     *
     * @param Address $data
     * @param string  $id   the address unique id
     */
    public function addAddress(Address $data, $id = null)
    {
        $packageId = $id !== null ? $id : count($this->addresses) + 1;
        $this->addresses['Address'][] = array_merge(['@attributes' => ['ID' => $packageId]], $data->getAddressInfo());
    }
    /**
     * Set the revision value
     *
     * @param string|int $value
     *
     * @return object AddressVerify
     */
    public function setRevision($value)
    {
        $this->revision = (string) $value;
        return $this;
    }
}
