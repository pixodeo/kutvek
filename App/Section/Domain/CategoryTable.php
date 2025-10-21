<?php
//declare(strict_types=1);
namespace App\Section\Domain;
use Core\Model\Table;

class CategoryTable extends Table {

    public function getContent(int $id): false|object {
        $this->setEntity('Content');
        $sql = "SELECT cat_l10n.designation,
            cat_l10n.short_desc,
            cat_l10n.description,
            cat_l10n.faq,
            cat_l10n.meta_title,
            cat_l10n.meta_description,
            cat_l10n.cover,
            cat_l10n.category,
            cat_l10n.breadcrumb,
            cat_l10n.further_info,
            params.behavior AS 'behavior_id',
            b.name AS 'behavior_name',
            lower(b._type) AS 'behavior_type',
            params.family,
            params.brand,
            params.model,
            params.vehicle,
            cat_l10n.l10n
            FROM category_l10ns cat_l10n
            LEFT JOIN vehicle_categories params ON params.id = cat_l10n.category
            LEFT JOIN behaviors b ON b.id = params.behavior
            WHERE cat_l10n.category = :id 
            AND cat_l10n.l10n = :l10n;
        ";
        $content = $this->query($sql, ['id'=>$id, 'l10n'=>$this->getL10nId()], true);
        if($content): 
            $content->setCurrency($this->getCurrency());
            $content->setI18n($this->getI18n());
            $content->setCountry($this->getCountry());
            $content->slugs = $this->slugs($content->category, $content->l10n);
        endif;
        return $content;
    }

    public function slugs(int $category, int $l10n){
        $this->setEntity(null);
        $sql = "SELECT cat_l10n.slug, 
        cat_l10n.l10n,       
        i18n.iso_639_1 AS 'short_designation',
        CASE WHEN cat_l10n.l10n = :current THEN 1 ELSE 0 END AS 'current' 
        FROM category_l10ns cat_l10n 
        LEFT JOIN l10ns l10n ON l10n.id = cat_l10n.l10n
        LEFT JOIN internationalisation i18n ON i18n.id = l10n.i18n
        WHERE category = :category";
        return $this->query($sql, ['category'=> $category, 'current'=> $l10n]);
    }

    public function categoryInfo(string $slug){
        $this->setEntity('Content');
        $sql = "SELECT 
            cat_l10n.category,
            params.behavior AS 'behavior_id',
            b.name AS 'behavior_name',
            lower(b._type) AS 'behavior_type',
            params.family,
            params.brand,
            params.model,
            params.vehicle,
            cat_l10n.l10n
            FROM category_l10ns cat_l10n
            LEFT JOIN vehicle_categories params ON params.id = cat_l10n.category
            LEFT JOIN behaviors b ON b.id = params.behavior
            WHERE cat_l10n.slug = :slug 
            AND cat_l10n.l10n = :l10n;
        ";
        $query = $this->query($sql, ['slug'=>$slug, 'l10n'=>$this->getL10nId()], true);
        if($query){
            $query->setCurrency($this->getCurrency());
            $query->setI18n($this->getI18n());
            $query->setCountry($this->getCountry());
        }
        return $query;

    }

    public function breadcrumbs_0(string $slug){
        $sql="SELECT b.`id`, b.`name`, b.`node_left`, b.`node_right`, b.`slug_type`, b.`slug`
        FROM `vue_slugs` b
        LEFT JOIN vue_slugs c ON (c.slug = :slug AND c.l10n = b.l10n AND c.slug_type = b.slug_type AND c.workspace = b.workspace)
        WHERE b.l10n = :l10n
        AND b.depth <= c.depth
        AND b.node_left <= c.node_left
        AND b.node_right >= c.node_right
        ORDER BY b.node_left";
        return $this->query($sql, ['slug'=>$slug, 'l10n'=>$this->getL10nId()]);
    }

    public function breadcrumbs(int $department){

        $sql = "SELECT name, slug, l10n
            FROM `vue_slugs`
            WHERE department IN (
            SELECT c.id
            FROM categories n 
            JOIN  categories c ON (c.node_left < n.node_left AND c.node_right > n.node_right AND c.workspace = n.workspace)
            WHERE n.id = :department
            AND c.parent IS NOT NULL
            ORDER BY c.node_left
            )
            AND l10n = :l10n;
        ";
        $parents = $this->query($sql, ['l10n'=>$this->getL10nId(), 'department' => $department]);
        $sql = "
            SELECT breadcrumb AS 'name', slug, l10n
            FROM category_l10ns
            WHERE category = :id
            AND l10n = :l10n
        ";
        $parents[] = $this->query($sql, ['id'=>$department,'l10n'=>$this->getL10nId()], true);
        return $parents;
    }

    public function childs(int $category){

        $sql = "SELECT child_l10n.category, child_l10n.designation, child_l10n.slug, child_l10n.breadcrumb, child.depth
            FROM categories as child
            LEFT JOIN category_l10ns child_l10n ON child_l10n.category = child.id AND child_l10n.l10n = :l10n
            JOIN categories as p
            WHERE p.id = :id
            AND child.node_left > p.node_left
            AND child.node_right < p.node_right
            AND child.workspace = p.workspace
            AND child.depth = p.depth+1
        ";
        return $this->query($sql, ['id'=>$category, 'l10n'=>$this->getL10nId()]);
    }



}