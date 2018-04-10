<?php
/**
 * Created by PhpStorm.
 * User: sasablagojevic
 * Date: 2/28/18
 * Time: 1:31 PM
 */

namespace Tests\Unit;

use Eusi\Auth\AccessToken;
use Eusi\Bucket\Client;
use Eusi\Delivery\HttpQuery;
use Eusi\Delivery\HttpQueryBuilder;
use PHPUnit\Framework\TestCase;


class HttpQueryBuilderTest extends TestCase
{
    /**
     * @return HttpQueryBuilder
     */
    protected function makeQueryBuilder()
    {
        return new HttpQueryBuilder(
            new Client('test-id', 'test-secret'),
            new HttpQuery(new AccessToken('xxx-xxx-xxx-xxx'))
        );
    }

    public function test_no_filters()
    {
        $this->assertSame('', $this->makeQueryBuilder()->getQueryString());
    }

    public function test_where_sys_name_equals_filter()
    {
        $this->assertSame("?sys.name=test", $this->makeQueryBuilder()->where("sys.name", "=", "test")->getQueryString());

        $this->assertSame("?sys.name=test", $this->makeQueryBuilder()->where("sys.name", "test")->getQueryString());

        $this->assertSame("?sys.name=test", $this->makeQueryBuilder()->whereName('test')->getQueryString());
    }

    public function test_where_sys_name_lesser_than_filter()
    {
        $this->assertSame('?sys.name[$lt]=test', $this->makeQueryBuilder()->where('sys.name', '<', 'test')->getQueryString());

        $this->assertSame('?sys.name[$lt]=test', $this->makeQueryBuilder()->where('name', '<', 'test')->getQueryString());

        $this->assertSame('?sys.name[$lt]=test-1&sys.name[$lt]=test-2', $this->makeQueryBuilder()->where('sys.name', '<', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_name_lesser_equals_than_filter()
    {
        $this->assertSame('?sys.name[$lte]=test', $this->makeQueryBuilder()->where('sys.name', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.name[$lte]=test', $this->makeQueryBuilder()->where('name', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.name[$lte]=test-1&sys.name[$lte]=test-2', $this->makeQueryBuilder()->where('sys.name', '<=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_name_greater_than_filter()
    {
        $this->assertSame('?sys.name[$gt]=test', $this->makeQueryBuilder()->where('sys.name', '>', 'test')->getQueryString());

        $this->assertSame('?sys.name[$gt]=test', $this->makeQueryBuilder()->where('name', '>', 'test')->getQueryString());

        $this->assertSame('?sys.name[$gt]=test-1&sys.name[$gt]=test-2', $this->makeQueryBuilder()->where('sys.name', '>', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_name_greater_equals_than_filter()
    {
        $this->assertSame('?sys.name[$gte]=test', $this->makeQueryBuilder()->where('sys.name', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.name[$gte]=test', $this->makeQueryBuilder()->where('name', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.name[$gte]=test-1&sys.name[$gte]=test-2', $this->makeQueryBuilder()->where('sys.name', '>=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_name_like_filter()
    {
        $this->assertSame('?sys.name[$like]=%test%', $this->makeQueryBuilder()->where('sys.name', 'like', '%test%')->getQueryString());

        $this->assertSame('?sys.name[$like]=%test%', $this->makeQueryBuilder()->where('name', '~', '%test%')->getQueryString());

        $this->assertSame(
            '?sys.name[$like]=%test-1%&sys.name[$like]=%test-2%',
            $this->makeQueryBuilder()->whereNameLike('%test-1%', '%test-2%')->getQueryString()
        );

        $this->assertSame(
            '?sys.name[$like]=%test-1%&sys.name[$like]=%test-2%',
            $this->makeQueryBuilder()->whereNameLike(['%test-1%', '%test-2%'])->getQueryString()
        );

        $this->assertSame(
            '?sys.name[$like]=%test-1%&sys.name[$like]=%test-2%',
            $this->makeQueryBuilder()->where('sys.name', 'like', ['%test-1%', '%test-2%'])->getQueryString()
        );
    }

    public function test_where_sys_name_in_filter()
    {
        $this->assertSame('?sys.name[$in]=test', $this->makeQueryBuilder()->where('sys.name', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.name[$in]=test', $this->makeQueryBuilder()->where('name', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.name[$in]=test-1&sys.name[$in]=test-2', $this->makeQueryBuilder()->whereNameIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.name[$in]=test-1&sys.name[$in]=test-2', $this->makeQueryBuilder()->whereNameIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.name[$in]=test-1&sys.name[$in]=test-2', $this->makeQueryBuilder()->where('sys.name', 'in', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_name_not_in_filter()
    {
        $this->assertSame('?sys.name[$ne]=test', $this->makeQueryBuilder()->where('sys.name', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.name[$ne]=test', $this->makeQueryBuilder()->where('name', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.name[$ne]=test-1&sys.name[$ne]=test-2', $this->makeQueryBuilder()->whereNameNotIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.name[$ne]=test-1&sys.name[$ne]=test-2', $this->makeQueryBuilder()->whereNameNotIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.name[$ne]=test-1&sys.name[$ne]=test-2', $this->makeQueryBuilder()->where('sys.name', '!=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_name_between_filter()
    {
        $this->assertSame('?sys.name[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.name', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.name[$between]=test-1,test-2', $this->makeQueryBuilder()->where('name', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.name[$between]=test-1,test-2', $this->makeQueryBuilder()->whereNameBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.name[$between]=test-1,test-2', $this->makeQueryBuilder()->whereNameBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.name[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.name', 'between', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_key_equals_filter()
    {
        $this->assertSame("?sys.key=test", $this->makeQueryBuilder()->where("sys.key", "=", "test")->getQueryString());

        $this->assertSame("?sys.key=test", $this->makeQueryBuilder()->where("sys.key", "test")->getQueryString());

        $this->assertSame("?sys.key=test", $this->makeQueryBuilder()->whereKey('test')->getQueryString());
    }

    public function test_where_sys_key_lesser_than_filter()
    {
        $this->assertSame('?sys.key[$lt]=test', $this->makeQueryBuilder()->where('sys.key', '<', 'test')->getQueryString());

        $this->assertSame('?sys.key[$lt]=test', $this->makeQueryBuilder()->where('key', '<', 'test')->getQueryString());

        $this->assertSame('?sys.key[$lt]=test-1&sys.key[$lt]=test-2', $this->makeQueryBuilder()->where('sys.key', '<', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_key_lesser_equals_than_filter()
    {
        $this->assertSame('?sys.key[$lte]=test', $this->makeQueryBuilder()->where('sys.key', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.key[$lte]=test', $this->makeQueryBuilder()->where('key', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.key[$lte]=test-1&sys.key[$lte]=test-2', $this->makeQueryBuilder()->where('sys.key', '<=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_key_greater_than_filter()
    {
        $this->assertSame('?sys.key[$gt]=test', $this->makeQueryBuilder()->where('sys.key', '>', 'test')->getQueryString());

        $this->assertSame('?sys.key[$gt]=test', $this->makeQueryBuilder()->where('key', '>', 'test')->getQueryString());

        $this->assertSame('?sys.key[$gt]=test-1&sys.key[$gt]=test-2', $this->makeQueryBuilder()->where('sys.key', '>', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_key_greater_equals_than_filter()
    {
        $this->assertSame('?sys.key[$gte]=test', $this->makeQueryBuilder()->where('sys.key', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.key[$gte]=test', $this->makeQueryBuilder()->where('key', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.key[$gte]=test-1&sys.key[$gte]=test-2', $this->makeQueryBuilder()->where('sys.key', '>=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_key_like_filter()
    {
        $this->assertSame('?sys.key[$like]=%test%', $this->makeQueryBuilder()->where('sys.key', 'like', '%test%')->getQueryString());

        $this->assertSame('?sys.key[$like]=%test%', $this->makeQueryBuilder()->where('key', '~', '%test%')->getQueryString());

        $this->assertSame(
            '?sys.key[$like]=%test-1%&sys.key[$like]=%test-2%',
            $this->makeQueryBuilder()->whereKeyLike('%test-1%', '%test-2%')->getQueryString()
        );

        $this->assertSame(
            '?sys.key[$like]=%test-1%&sys.key[$like]=%test-2%',
            $this->makeQueryBuilder()->whereKeyLike(['%test-1%', '%test-2%'])->getQueryString()
        );

        $this->assertSame(
            '?sys.key[$like]=%test-1%&sys.key[$like]=%test-2%',
            $this->makeQueryBuilder()->where('sys.key', 'like', ['%test-1%', '%test-2%'])->getQueryString()
        );
    }

    public function test_where_sys_key_in_filter()
    {
        $this->assertSame('?sys.key[$in]=test', $this->makeQueryBuilder()->where('sys.key', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.key[$in]=test', $this->makeQueryBuilder()->where('key', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.key[$in]=test-1&sys.key[$in]=test-2', $this->makeQueryBuilder()->whereKeyIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.key[$in]=test-1&sys.key[$in]=test-2', $this->makeQueryBuilder()->whereKeyIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.key[$in]=test-1&sys.key[$in]=test-2', $this->makeQueryBuilder()->where('sys.key', 'in', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_key_not_in_filter()
    {
        $this->assertSame('?sys.key[$ne]=test', $this->makeQueryBuilder()->where('sys.key', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.key[$ne]=test', $this->makeQueryBuilder()->where('key', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.key[$ne]=test-1&sys.key[$ne]=test-2', $this->makeQueryBuilder()->whereKeyNotIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.key[$ne]=test-1&sys.key[$ne]=test-2', $this->makeQueryBuilder()->whereKeyNotIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.key[$ne]=test-1&sys.key[$ne]=test-2', $this->makeQueryBuilder()->where('sys.key', '!=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_key_between_filter()
    {
        $this->assertSame('?sys.key[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.key', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.key[$between]=test-1,test-2', $this->makeQueryBuilder()->where('key', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.key[$between]=test-1,test-2', $this->makeQueryBuilder()->whereKeyBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.key[$between]=test-1,test-2', $this->makeQueryBuilder()->whereKeyBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.key[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.key', 'between', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_type_equals_filter()
    {
        $this->assertSame("?sys.type=test", $this->makeQueryBuilder()->where("sys.type", "=", "test")->getQueryString());

        $this->assertSame("?sys.type=test", $this->makeQueryBuilder()->where("sys.type", "test")->getQueryString());

        $this->assertSame("?sys.type=test", $this->makeQueryBuilder()->whereType('test')->getQueryString());
    }

    public function test_where_sys_type_lesser_than_filter()
    {
        $this->assertSame('?sys.type[$lt]=test', $this->makeQueryBuilder()->where('sys.type', '<', 'test')->getQueryString());

        $this->assertSame('?sys.type[$lt]=test', $this->makeQueryBuilder()->where('type', '<', 'test')->getQueryString());

        $this->assertSame('?sys.type[$lt]=test-1&sys.type[$lt]=test-2', $this->makeQueryBuilder()->where('sys.type', '<', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_type_lesser_equals_than_filter()
    {
        $this->assertSame('?sys.type[$lte]=test', $this->makeQueryBuilder()->where('sys.type', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.type[$lte]=test', $this->makeQueryBuilder()->where('type', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.type[$lte]=test-1&sys.type[$lte]=test-2', $this->makeQueryBuilder()->where('sys.type', '<=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_type_greater_than_filter()
    {
        $this->assertSame('?sys.type[$gt]=test', $this->makeQueryBuilder()->where('sys.type', '>', 'test')->getQueryString());

        $this->assertSame('?sys.type[$gt]=test', $this->makeQueryBuilder()->where('type', '>', 'test')->getQueryString());

        $this->assertSame('?sys.type[$gt]=test-1&sys.type[$gt]=test-2', $this->makeQueryBuilder()->where('sys.type', '>', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_type_greater_equals_than_filter()
    {
        $this->assertSame('?sys.type[$gte]=test', $this->makeQueryBuilder()->where('sys.type', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.type[$gte]=test', $this->makeQueryBuilder()->where('type', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.type[$gte]=test-1&sys.type[$gte]=test-2', $this->makeQueryBuilder()->where('sys.type', '>=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_type_like_filter()
    {
        $this->assertSame('?sys.type[$like]=%test%', $this->makeQueryBuilder()->where('sys.type', 'like', '%test%')->getQueryString());

        $this->assertSame('?sys.type[$like]=%test%', $this->makeQueryBuilder()->where('type', '~', '%test%')->getQueryString());

        $this->assertSame('?sys.type[$like]=%test-1%&sys.type[$like]=%test-2%', $this->makeQueryBuilder()->whereTypeLike('%test-1%', '%test-2%')->getQueryString());

        $this->assertSame('?sys.type[$like]=%test-1%&sys.type[$like]=%test-2%', $this->makeQueryBuilder()->whereTypeLike(['%test-1%', '%test-2%'])->getQueryString());

        $this->assertSame('?sys.type[$like]=%test-1%&sys.type[$like]=%test-2%', $this->makeQueryBuilder()->where('sys.type', 'like', ['%test-1%', '%test-2%'])->getQueryString());
    }

    public function test_where_sys_type_in_filter()
    {
        $this->assertSame('?sys.type[$in]=test', $this->makeQueryBuilder()->where('sys.type', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.type[$in]=test', $this->makeQueryBuilder()->where('type', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.type[$in]=test-1&sys.type[$in]=test-2', $this->makeQueryBuilder()->whereTypeIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.type[$in]=test-1&sys.type[$in]=test-2', $this->makeQueryBuilder()->whereTypeIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.type[$in]=test-1&sys.type[$in]=test-2', $this->makeQueryBuilder()->where('sys.type', 'in', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_type_not_in_filter()
    {
        $this->assertSame('?sys.type[$ne]=test', $this->makeQueryBuilder()->where('sys.type', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.type[$ne]=test', $this->makeQueryBuilder()->where('type', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.type[$ne]=test-1&sys.type[$ne]=test-2', $this->makeQueryBuilder()->whereTypeNotIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.type[$ne]=test-1&sys.type[$ne]=test-2', $this->makeQueryBuilder()->whereTypeNotIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.type[$ne]=test-1&sys.type[$ne]=test-2', $this->makeQueryBuilder()->where('sys.type', '!=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_sys_type_between_filter()
    {
        $this->assertSame('?sys.type[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.type', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.type[$between]=test-1,test-2', $this->makeQueryBuilder()->where('type', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.type[$between]=test-1,test-2', $this->makeQueryBuilder()->whereTypeBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.type[$between]=test-1,test-2', $this->makeQueryBuilder()->whereTypeBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.type[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.type', 'between', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_equals_filter()
    {
        $this->assertSame("?sys.taxonomy=test", $this->makeQueryBuilder()->where("sys.taxonomy", "=", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy=test", $this->makeQueryBuilder()->where("taxonomy", "=", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy=test", $this->makeQueryBuilder()->where("tax", "=", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy=test", $this->makeQueryBuilder()->where("sys.taxonomy", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy=test", $this->makeQueryBuilder()->whereTax('test')->getQueryString());
    }

    public function test_where_taxonomy_lesser_than_filter()
    {
        $this->assertSame('?sys.taxonomy[$lt]=test', $this->makeQueryBuilder()->where('sys.taxonomy', '<', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$lt]=test', $this->makeQueryBuilder()->where('taxonomy', '<', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$lt]=test-1&sys.taxonomy[$lt]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy', '<', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_lesser_equals_than_filter()
    {
        $this->assertSame('?sys.taxonomy[$lte]=test', $this->makeQueryBuilder()->where('sys.taxonomy', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$lte]=test', $this->makeQueryBuilder()->where('taxonomy', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$lte]=test-1&sys.taxonomy[$lte]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy', '<=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_greater_than_filter()
    {
        $this->assertSame('?sys.taxonomy[$gt]=test', $this->makeQueryBuilder()->where('sys.taxonomy', '>', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$gt]=test', $this->makeQueryBuilder()->where('taxonomy', '>', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$gt]=test-1&sys.taxonomy[$gt]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy', '>', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_greater_equals_than_filter()
    {
        $this->assertSame('?sys.taxonomy[$gte]=test', $this->makeQueryBuilder()->where('sys.taxonomy', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$gte]=test', $this->makeQueryBuilder()->where('taxonomy', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$gte]=test-1&sys.taxonomy[$gte]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy', '>=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_like_filter()
    {
        $this->assertSame('?sys.taxonomy[$like]=%test%', $this->makeQueryBuilder()->where('sys.taxonomy', 'like', '%test%')->getQueryString());

        $this->assertSame('?sys.taxonomy[$like]=%test%', $this->makeQueryBuilder()->where('taxonomy', '~', '%test%')->getQueryString());

        $this->assertSame('?sys.taxonomy[$like]=%test-1%&sys.taxonomy[$like]=%test-2%', $this->makeQueryBuilder()->whereTaxLike('%test-1%', '%test-2%')->getQueryString());

        $this->assertSame('?sys.taxonomy[$like]=%test-1%&sys.taxonomy[$like]=%test-2%', $this->makeQueryBuilder()->whereTaxLike(['%test-1%', '%test-2%'])->getQueryString());

        $this->assertSame('?sys.taxonomy[$like]=%test-1%&sys.taxonomy[$like]=%test-2%', $this->makeQueryBuilder()->where('taxonomy', 'like', ['%test-1%', '%test-2%'])->getQueryString());
    }

    public function test_where_taxonomy_in_filter()
    {
        $this->assertSame('?sys.taxonomy[$in]=test', $this->makeQueryBuilder()->where('sys.taxonomy', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$in]=test', $this->makeQueryBuilder()->where('taxonomy', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$in]=test-1&sys.taxonomy[$in]=test-2', $this->makeQueryBuilder()->whereTaxIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.taxonomy[$in]=test-1&sys.taxonomy[$in]=test-2', $this->makeQueryBuilder()->whereTaxIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy[$in]=test-1&sys.taxonomy[$in]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy', 'in', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_not_in_filter()
    {
        $this->assertSame('?sys.taxonomy[$ne]=test', $this->makeQueryBuilder()->where('sys.taxonomy', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$ne]=test', $this->makeQueryBuilder()->where('taxonomy', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy[$ne]=test-1&sys.taxonomy[$ne]=test-2', $this->makeQueryBuilder()->whereTaxNotIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.taxonomy[$ne]=test-1&sys.taxonomy[$ne]=test-2', $this->makeQueryBuilder()->whereTaxNotIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy[$ne]=test-1&sys.taxonomy[$ne]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy', '!=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_between_filter()
    {
        $this->assertSame('?sys.taxonomy[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.taxonomy', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy[$between]=test-1,test-2', $this->makeQueryBuilder()->where('taxonomy', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy[$between]=test-1,test-2', $this->makeQueryBuilder()->whereTaxBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy[$between]=test-1,test-2', $this->makeQueryBuilder()->whereTaxBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.taxonomy', 'between', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_path_equals_filter()
    {
        $this->assertSame("?sys.taxonomy.path=test", $this->makeQueryBuilder()->where("sys.taxonomy.path", "=", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy.path=test", $this->makeQueryBuilder()->where("taxonomy.path", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy.path=test", $this->makeQueryBuilder()->where("tax.path", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy.path=test", $this->makeQueryBuilder()->where("sys.taxonomy.path", "test")->getQueryString());

        $this->assertSame("?sys.taxonomy.path=test", $this->makeQueryBuilder()->whereTaxPath('test')->getQueryString());
    }

    public function test_where_taxonomy_path_lesser_than_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$lt]=test', $this->makeQueryBuilder()->where('sys.taxonomy.path', '<', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$lt]=test', $this->makeQueryBuilder()->where('taxonomy.path', '<', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$lt]=test-1&sys.taxonomy.path[$lt]=test-2', $this->makeQueryBuilder()->where('tax.path', '<', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_path_lesser_equals_than_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$lte]=test', $this->makeQueryBuilder()->where('sys.taxonomy.path', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$lte]=test', $this->makeQueryBuilder()->where('taxonomy.path', '<=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$lte]=test-1&sys.taxonomy.path[$lte]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy.path', '<=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_path_greater_than_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$gt]=test', $this->makeQueryBuilder()->where('sys.taxonomy.path', '>', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$gt]=test', $this->makeQueryBuilder()->where('taxonomy.path', '>', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$gt]=test-1&sys.taxonomy.path[$gt]=test-2', $this->makeQueryBuilder()->where('tax.path', '>', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_path_greater_equals_than_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$gte]=test', $this->makeQueryBuilder()->where('sys.taxonomy.path', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$gte]=test', $this->makeQueryBuilder()->where('taxonomy.path', '>=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$gte]=test-1&sys.taxonomy.path[$gte]=test-2', $this->makeQueryBuilder()->where('taxonomy.path', '>=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_path_like_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$like]=%test%', $this->makeQueryBuilder()->where('sys.taxonomy.path', 'like', '%test%')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$like]=%test%', $this->makeQueryBuilder()->where('taxonomy.path', '~', '%test%')->getQueryString());

        $this->assertSame(
            '?sys.taxonomy.path[$like]=%test-1%&sys.taxonomy.path[$like]=%test-2%',
            $this->makeQueryBuilder()->whereTaxPathLike('%test-1%', '%test-2%')->getQueryString()
        );

        $this->assertSame(
            '?sys.taxonomy.path[$like]=%test-1%&sys.taxonomy.path[$like]=%test-2%',
            $this->makeQueryBuilder()->whereTaxPathLike(['%test-1%', '%test-2%'])->getQueryString()
        );

        $this->assertSame(
            '?sys.taxonomy.path[$like]=%test-1%&sys.taxonomy.path[$like]=%test-2%',
            $this->makeQueryBuilder()->where('taxonomy.path', 'like', ['%test-1%', '%test-2%'])->getQueryString()
        );
    }

    public function test_where_taxonomy_path_in_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$in]=test', $this->makeQueryBuilder()->where('sys.taxonomy.path', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$in]=test', $this->makeQueryBuilder()->where('taxonomy.path', 'in', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$in]=test-1&sys.taxonomy.path[$in]=test-2', $this->makeQueryBuilder()->whereTaxPathIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$in]=test-1&sys.taxonomy.path[$in]=test-2', $this->makeQueryBuilder()->whereTaxPathIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$in]=test-1&sys.taxonomy.path[$in]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy.path', 'in', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_path_not_in_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$ne]=test', $this->makeQueryBuilder()->where('sys.taxonomy.path', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$ne]=test', $this->makeQueryBuilder()->where('taxonomy.path', '!=', 'test')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$ne]=test-1&sys.taxonomy.path[$ne]=test-2', $this->makeQueryBuilder()->whereTaxPathNotIn('test-1', 'test-2')->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$ne]=test-1&sys.taxonomy.path[$ne]=test-2', $this->makeQueryBuilder()->whereTaxPathNotIn(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$ne]=test-1&sys.taxonomy.path[$ne]=test-2', $this->makeQueryBuilder()->where('sys.taxonomy.path', '!=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_taxonomy_path_between_filter()
    {
        $this->assertSame('?sys.taxonomy.path[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.taxonomy.path', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$between]=test-1,test-2', $this->makeQueryBuilder()->where('taxonomy.path', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$between]=test-1,test-2', $this->makeQueryBuilder()->whereTaxPathBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$between]=test-1,test-2', $this->makeQueryBuilder()->whereTaxPathBetween(['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?sys.taxonomy.path[$between]=test-1,test-2', $this->makeQueryBuilder()->where('sys.taxonomy.path', 'between', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_element_equals_filter()
    {
        $this->assertSame("?element.prop=test", $this->makeQueryBuilder()->where("element.prop", "=", "test")->getQueryString());

        $this->assertSame("?element.prop=test", $this->makeQueryBuilder()->where("element.prop", "test")->getQueryString());

        $this->assertSame("?element.prop=test", $this->makeQueryBuilder()->whereElement('prop', 'test')->getQueryString());
    }

    public function test_where_element_lesser_than_filter()
    {
        $this->assertSame('?element.prop[$lt]=test', $this->makeQueryBuilder()->where('element.prop', '<', 'test')->getQueryString());

        $this->assertSame('?element.prop[$lt]=test-1&element.prop[$lt]=test-2', $this->makeQueryBuilder()->where('element.prop', '<', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_element_lesser_equals_than_filter()
    {
        $this->assertSame('?element.prop[$lte]=test', $this->makeQueryBuilder()->where('element.prop', '<=', 'test')->getQueryString());

        $this->assertSame('?element.prop[$lte]=test-1&element.prop[$lte]=test-2', $this->makeQueryBuilder()->where('element.prop', '<=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_element_greater_than_filter()
    {
        $this->assertSame('?element.prop[$gt]=test', $this->makeQueryBuilder()->where('element.prop', '>', 'test')->getQueryString());

        $this->assertSame('?element.prop[$gt]=test-1&element.prop[$gt]=test-2', $this->makeQueryBuilder()->where('element.prop', '>', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_element_greater_equals_than_filter()
    {
        $this->assertSame('?element.prop[$gte]=test', $this->makeQueryBuilder()->where('element.prop', '>=', 'test')->getQueryString());

        $this->assertSame('?element.prop[$gte]=test-1&element.prop[$gte]=test-2', $this->makeQueryBuilder()->where('element.prop', '>=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_element_like_filter()
    {
        $this->assertSame('?element.test[$like]=%test%', $this->makeQueryBuilder()->where('element.test', 'like', '%test%')->getQueryString());

        $this->assertSame('?element.test[$like]=%test%', $this->makeQueryBuilder()->where('element.test', '~', '%test%')->getQueryString());

        $this->assertSame(
            '?element.test[$like]=%test-1%&element.test[$like]=%test-2%',
            $this->makeQueryBuilder()->whereElementLike('test', '%test-1%', '%test-2%')->getQueryString()
        );

        $this->assertSame(
            '?element.test[$like]=%test-1%&element.test[$like]=%test-2%',
            $this->makeQueryBuilder()->whereElementLike('test', ['%test-1%', '%test-2%'])->getQueryString()
        );

        $this->assertSame(
            '?element.test[$like]=%test-1%&element.test[$like]=%test-2%',
            $this->makeQueryBuilder()->where('element.test', 'like', ['%test-1%', '%test-2%'])->getQueryString()
        );
    }

    public function test_where_element_in_filter()
    {
        $this->assertSame('?element.prop[$in]=test', $this->makeQueryBuilder()->where('element.prop', 'in', 'test')->getQueryString());

        $this->assertSame('?element.prop[$in]=test-1&element.prop[$in]=test-2', $this->makeQueryBuilder()->whereElementIn('prop', 'test-1', 'test-2')->getQueryString());

        $this->assertSame('?element.prop[$in]=test-1&element.prop[$in]=test-2', $this->makeQueryBuilder()->whereElementIn('prop', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?element.prop[$in]=test-1&element.prop[$in]=test-2', $this->makeQueryBuilder()->where('element.prop', 'in', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_element_not_in_filter()
    {
        $this->assertSame('?element.prop[$ne]=test', $this->makeQueryBuilder()->where('element.prop', '!=', 'test')->getQueryString());

        $this->assertSame('?element.prop[$ne]=test-1&element.prop[$ne]=test-2', $this->makeQueryBuilder()->whereElementNotIn('prop', 'test-1', 'test-2')->getQueryString());

        $this->assertSame('?element.prop[$ne]=test-1&element.prop[$ne]=test-2', $this->makeQueryBuilder()->whereElementNotIn('prop', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?element.prop[$ne]=test-1&element.prop[$ne]=test-2', $this->makeQueryBuilder()->where('element.prop', '!=', ['test-1', 'test-2'])->getQueryString());
    }

    public function test_where_element_between_filter()
    {
        $this->assertSame('?element.prop[$between]=test-1,test-2', $this->makeQueryBuilder()->where('element.prop', 'btw', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?element.prop[$between]=test-1,test-2', $this->makeQueryBuilder()->whereElementBetween('prop', 'test-1', 'test-2')->getQueryString());

        $this->assertSame('?element.prop[$between]=test-1,test-2', $this->makeQueryBuilder()->whereElementBetween('prop', ['test-1', 'test-2'])->getQueryString());

        $this->assertSame('?element.prop[$between]=test-1,test-2', $this->makeQueryBuilder()->where('element.prop', 'between', ['test-1', 'test-2'])->getQueryString());
    }
}