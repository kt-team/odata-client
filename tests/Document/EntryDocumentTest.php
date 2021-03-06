<?php
/**
 * OData client library
 *
 * @author  Михаил Красильников <m.krasilnikov@yandex.ru>
 * @license MIT
 */
namespace Mekras\OData\Client\Tests\Document;

use Mekras\OData\Client\Document\EntryDocument;
use Mekras\OData\Client\DocumentFactory;
use Mekras\OData\Client\Element\Entry;
use Mekras\OData\Client\Element\Properties;
use Mekras\OData\Client\Tests\TestCase;

/**
 * Tests for Mekras\OData\Document\EntryDocument
 */
class EntryDocumentTest extends TestCase
{
    /**
     * EntryDocument::getEntry should return an instance of OData\Entry
     */
    public function testParse()
    {
        $factory = new DocumentFactory();
        /** @var EntryDocument $document */
        $document = $factory->parseXML($this->loadFixture('EntryDocument.xml'));

        static::assertInstanceOf(EntryDocument::class, $document);
        /** @var Entry $entry */
        $entry = $document->getEntry();
        static::assertInstanceOf(Entry::class, $entry);

        static::assertEquals(
            'http://services.odata.org/OData/OData.svc/Categories(0)',
            $entry->getId()
        );
        static::assertEquals('ODataDemo.Category', $entry->getEntityType());
        static::assertEquals('Categories(0)', (string) $entry->getLink('edit'));
        static::assertEquals('Food', (string) $entry->getTitle());
        static::assertEquals(
            '10.03.10 10:43:51',
            $entry->getUpdated()->getDate()->format('d.m.y H:i:s')
        );
        $content = $entry->getContent();
        static::assertEquals('application/xml', $content->getType());

        static::assertEquals(0, $entry['ID']->getValue());
    }

    /**
     * Test creating entries.
     */
    public function testCreate()
    {
        $document = $this->createService()->getDocumentFactory()->createEntityDocument('FooModel');

        static::assertEquals(
            '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<entry xmlns="http://www.w3.org/2005/Atom" ' .
            'xmlns:xhtml="http://www.w3.org/1999/xhtml" ' .
            'xmlns:app="http://www.w3.org/2007/app" ' .
            'xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata" ' .
            'xmlns:d="http://schemas.microsoft.com/ado/2007/08/dataservices">' .
            '<author><name></name></author>' .
            '<content type="application/xml"><m:properties/></content>' .
            '<category term="FooModel" ' .
            'scheme="http://schemas.microsoft.com/ado/2007/08/dataservices/scheme"/>' .
            '</entry>',
            trim($document)
        );
    }

    /**
     * Empty entry should contains m:properties node.
     */
    public function testEmptyEntryHasProperties()
    {
        $document = new EntryDocument($this->createExtensions());
        $entry = $document->getEntry();
        static::assertInstanceOf(Properties::class, $entry->getProperties());
    }
}
