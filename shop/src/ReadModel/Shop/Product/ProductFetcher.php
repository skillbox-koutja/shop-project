<?php

namespace App\ReadModel\Shop\Product;

use App\Model\Shop\Entity\Category;
use App\Model\Shop\Entity\Product;
use App\ReadModel\Shared\FetcherMetaHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProductFetcher
{
    private Connection $connection;
    private PaginatorInterface $paginator;
    private ClassMetadata $productMeta;
    private ClassMetadata $categoryMeta;
    private ClassMetadata $photoMeta;

    public function __construct(
        Connection $connection,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    )
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
        $this->categoryMeta = $em->getClassMetadata(Category\Category::class);
        $this->productMeta = $em->getClassMetadata(Product\Product::class);
        $this->photoMeta = $em->getClassMetadata(Product\Photo\Photo::class);
    }

    public function maxPrice(array $categories = [])
    {
        return $this->price('max', $categories);
    }

    public function minPrice(array $categories = [])
    {
        return $this->price('min', $categories);
    }

    /**
     * @param Filter\Filter $filter
     * @param Sorter\Sorter $sorter
     * @param int $page
     * @param int $size
     * @return PaginationInterface
     */
    public function feed(
        Filter\Filter $filter,
        Sorter\Sorter $sorter,
        int $page,
        int $size
    ): PaginationInterface
    {
        $conn = $this->connection;
        $helper = new FetcherMetaHelper($this->productMeta, 'p');
        $photoHelper = new FetcherMetaHelper($this->photoMeta, 'photo');
        $qb = $helper->from($conn->createQueryBuilder());
        $expr = $qb->expr();
        $title = $helper->select('title');
        $qb->select([
            $helper->select('id', $conn->quoteIdentifier('id')),
            $helper->select('title', $conn->quoteIdentifier('title')),
            $helper->select('price', $conn->quoteIdentifier('price')),
            $photoHelper->select('id', $conn->quoteIdentifier('photo_id')),
            $photoHelper->selectColumn('photo', 'photo_path', $conn->quoteIdentifier('photo_path')),
            $photoHelper->selectColumn('photo', 'photo_title', $conn->quoteIdentifier('photo_title')),
        ]);
        if (isset($filter->min) && isset($filter->max)) {
            $price = $helper->select('price');
            $qb->andWhere($qb->expr()->gte($price, ':minPrice'));
            $qb->setParameter(':minPrice', $filter->min);
            $qb->andWhere($qb->expr()->lte($price, ':maxPrice'));
            $qb->setParameter(':maxPrice', $filter->max);
        }

        $qb->innerJoin('p',
            $this->photoMeta->getTableName(), 'photo',
            $expr->eq(
                $helper->select('id'),
                $photoHelper->selectColumn('photo', 'product_id'),
            )
        );
        if (isset($filter->title)) {
            $qb->andWhere($qb->expr()->like("LOWER({$title})", ':title'));
            $qb->setParameter(':title', '%' . mb_strtolower($filter->title) . '%');
        }

        if (!empty($filter->categories)) {
            $productId = $helper->selectColumn('cp', 'product_id');
            $categoriesQb = $conn->createQueryBuilder()
                ->select($productId)
                ->from($helper->tableManyToMany('categories'), 'cp')
                ->groupBy($productId);
            foreach ($filter->categories as $index => $categoryId) {
                $key = ":cp_cat{$index}";
                $productInCategoryQb = $this->productInCategoryQb($helper, $key);
                $categoriesQb->andWhere(
                    $expr->in(
                        $productId,
                        '(' . $productInCategoryQb ->getSQL(). ')',
                    )
                );
                $qb->setParameter($key, $categoryId);
            }
            $qb->andWhere($expr->in(
                $helper->select('id'),
                '(' . $categoriesQb ->getSQL(). ')',
            ));
        }

        $helper->addOrderBy($qb, $sorter);

        return $this->paginator->paginate($qb, $page, $size);
    }

    private function productInCategoryQb(
        FetcherMetaHelper $helper,
        $idKey
    ): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('product_id')
            ->from($helper->tableManyToMany('categories'));
        $qb->where(
            $qb->expr()->eq('category_id',$idKey)
        );

        return $qb;
    }

    /**
     * @param Filter\Filter $filter
     * @param Sorter\Sorter $sorter
     * @param int $page
     * @param int $size
     * @param string $sort
     * @param int $direction
     * @return PaginationInterface
     */
    public function all(
        Filter\Filter $filter,
        Sorter\Sorter $sorter,
        int $page,
        int $size
    ): PaginationInterface
    {
        $conn = $this->connection;
        $helper = new FetcherMetaHelper($this->productMeta, 'p');
        $qb = $helper->from($conn->createQueryBuilder());
        $title = $helper->select('title');
        $qb->select([
            $helper->select('id', $conn->quoteIdentifier('id')),
            $helper->select('title', $conn->quoteIdentifier('title')),
            $helper->select('price', $conn->quoteIdentifier('price')),
        ]);

        if (isset($filter->title)) {
            $qb->andWhere($qb->expr()->like("LOWER({$title})", ':title'));
            $qb->setParameter(':title', '%' . mb_strtolower($filter->title) . '%');
        }

//        if ($filter->categories) {
//            $qb->andWhere('p.status = :status');
//            $qb->setParameter(':status', $filter->status);
//        }

        $helper->addOrderBy($qb, $sorter);

        $pagination = $this->paginator->paginate($qb, $page, $size);

        $products = (array)$pagination->getItems();
        $categories = $this->batchLoadCategories(array_column($products, 'id'));
        $pagination->setItems(
            array_map(
                static function (array $product) use ($categories) {
                    $new = false;
                    $sale = false;
                    $items = [];
                    foreach ($categories as $category) {
                        if ($category['product_id'] === $product['id']) {
                            if (Category\Type::NEW === $category['slug']) {
                                $new = true;
                                continue;
                            }
                            if (Category\Type::SALE === $category['slug']) {
                                $sale = true;
                                continue;
                            } else {
                                $items[] = $category;
                            }
                        }
                    }
                    return array_merge(
                        $product,
                        [
                            'categories' => $items,
                            'new' => $new,
                            'sale' => $sale,
                        ]
                    );
                }, $products));

        return $pagination;
    }

    private function batchLoadCategories($ids): array
    {
        $conn = $this->connection;
        $helper = new FetcherMetaHelper($this->productMeta, 'p');
        $categoryMetaHelper = new FetcherMetaHelper($this->categoryMeta, 'c');
        $qb = $conn->createQueryBuilder();
        $expr = $qb->expr();
        $productId = $helper->selectColumn('a', 'product_id');
        $qb = $helper->fromManyToMany($qb, 'categories', 'a')
            ->select([
                $helper->selectColumn('a', 'product_id', $conn->quoteIdentifier('product_id')),
                $categoryMetaHelper->select('slug', $conn->quoteIdentifier('slug')),
                $categoryMetaHelper->select('title', $conn->quoteIdentifier('title')),
            ])
            ->innerJoin('a',
                $this->categoryMeta->getTableName(), 'c',
                $expr->eq(
                    $categoryMetaHelper->select('id'),
                    $helper->selectColumn('a', 'category_id'),
                )
            )
            ->andWhere($expr->in($productId, ':products'))
            ->setParameter(':products', $ids, Connection::PARAM_INT_ARRAY)
            ->orderBy($categoryMetaHelper->select('title'));

        /** @var Statement $stmt */
        $stmt = $qb->execute();

        return $stmt->fetchAllAssociative();
    }

    private function price(string $operator, array $categories = [])
    {
        $conn = $this->connection;
        $helper = new FetcherMetaHelper($this->productMeta, 'p');
        $qb = $helper->from($conn->createQueryBuilder());
        $column = $helper->select('price');
        $expr = $qb->expr();
        $qb->select([
            "{$operator}({$column})"
        ]);
        if (!empty($categories)) {
            $qb->innerJoin('p',
                $helper->tableManyToMany('categories'), 'cp',
                $expr->eq(
                    $helper->select('id'),
                    $helper->selectColumn('cp', 'product_id'),
                )
            );
            $qb->andWhere($expr->in(
                $helper->selectColumn('cp', 'category_id'),
                ':categories',
            ));
            $qb->setParameter(':categories', $categories, Connection::PARAM_STR_ARRAY);
        }

        $stmt = $this->getResult($qb);

        return $stmt->fetchOne();
    }
    /**
     * @return Statement|ResultStatement
     */
    private function getResult(QueryBuilder $qb)
    {
        return $qb->execute();
    }
}
