<?php

declare(strict_types=1);

namespace BitBag\SyliusGraphqlPlugin\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class AddressInput extends InputObjectType
{
    public function getName(): string
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->name = 'AddressInput';
        $this->description = 'The `Address` object type.';
        $config = [
            'fields' => [
                'id' => Type::id(),
                'firstName' => Type::string(),
                'lastName' => Type::string(),
                'fullName' => Type::string(),
                'countryCode' => Type::string(),
                'provinceCode' => Type::string(),
                'phoneNumber' => Type::string(),
                'street' => Type::string(),
                'city' => Type::string(),
                'company' => Type::string(),
                'postcode' => Type::string(),
            ],
        ];
        parent::__construct($config);
    }
}