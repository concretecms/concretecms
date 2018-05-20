<?php
namespace Concrete\Core\Entity\User;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class AttributeRepository extends EntityRepository
{


    /**
     * Method that return query builder to get all User Attributes key Available for one or several user groups and
     * return common attributes available for all user if $groups is empty
     * @param \Group [] $groups
     * @param boolean $twice if twice is true we return all attributes (common and specific for group received as parameter)
     * @return QueryBuilder
     */
    protected function getUserAttributesBasicQueryBuilder($groups=array(), $twice=false)
    {
        $gIDs = array();
        foreach ($groups as $group) {
            $gIDs[] = $group->getGroupID();
        }
        $qb=$this->createQueryBuilder("ak", 'ak.akID');
        $qb ->leftJoin("ak.userKeyPerUserGroups", "ukug");
        if ($twice && count($gIDs)>0) {
            $qb->where($qb->expr()->orX($qb->expr()->isNull("ukug.gID"), $qb->expr()->in("ukug.gID", $gIDs)));
        } else {
            if (count($groups) == 0) {
                $qb->
                where($qb->expr()->isNull("ukug.gID"));
            } else {
                $qb->where($qb->expr()->in("ukug.gID", implode($gIDs)));
            }
        }
        return $qb;
    }


    /**
     * @param $fieldName
     * @param $groups
     * @return array
     */
    private function sharedQueryBuilderToGetCommonAndAssociatedToGroupKeyList($fieldName, $groups=array())
    {
        $aks=array();
        $qb=$this->getUserAttributesBasicQueryBuilder($groups, true);
        $qb->groupBy("ak.akID")
            ->select("ak,ak.akID ,max(ukug.$fieldName),ak.$fieldName")
            ->having(
                $qb->expr()->orX(
                $qb->expr()->eq("max(ukug.$fieldName)", 1),
                $qb->expr()->andX(
                    $qb->expr()->isNull("max(ukug.$fieldName)"),
                    $qb->expr()->eq("ak.$fieldName", 1)
                )
            )
            );
        $q=$qb->getQuery()->getSQL();
        $results=$qb->getQuery()->execute();
        if (count($results)>0) {
            foreach ($results as $mixedResult) {
                $aks[$mixedResult['akID']]=$mixedResult[0];
            }
        }
        return $aks;
    }


    /**
     * Method that return common and associated to groups User key list
     * @param array $groups
     * @return mixed
     */
    public function getList($groups=array())
    {
        $qb=$this->getUserAttributesBasicQueryBuilder($groups, true);
        $qb
            ->andWhere($qb->expr()->eq("ak.akIsInternal", 0));
        return $qb->getQuery()->execute();
    }
    /**
     * Method that return common and associated to groups User key list available on registration form
     * @param array $groups
     * @return mixed
     */
    public function getRegistrationList($groups=array())
    {
        return $this->sharedQueryBuilderToGetCommonAndAssociatedToGroupKeyList("uakRegisterEdit", $groups);
    }

    /**
     * Method that return common and associated to groups User key list available on member list
     * @param $groups
     * @return mixed
     */
    public function getMemberListList($groups)
    {
        return $this->sharedQueryBuilderToGetCommonAndAssociatedToGroupKeyList("uakMemberListDisplay", $groups);
    }



    /**
     * @param $groups
     * @return mixed
     */
    public function getPublicProfileList($groups)
    {
        return $this->sharedQueryBuilderToGetCommonAndAssociatedToGroupKeyList("uakProfileDisplay", $groups);
    }

    /**
     * @param $groups
     * @return mixed
     */
    public function getEditableInProfileList($groups)
    {
        return $this->sharedQueryBuilderToGetCommonAndAssociatedToGroupKeyList("uakProfileEdit", $groups);
    }

    /**
     * Method that return all Common User Attribute (Attributes key not associated with any group
     * @return mixed
     */
    public function getCommonList()
    {
        $qb=$this->getUserAttributesBasicQueryBuilder();
        $qb->andWhere($qb->expr()->eq("ak.akIsInternal", 0));
        return $qb->getQuery()->execute();
    }

    /**
     * Method that return all user common attribute are available in register form
     * @return mixed
     */
    public function getCommonRegistrationList()
    {
        $qb=$this->getUserAttributesBasicQueryBuilder();
        $qb->andWhere($qb->expr()->eq("ak.uakRegisterEdit", 1));
        return $qb->getQuery()->execute();
    }

    /**
     * Method that return all user common attribute are available in view list
     * @return mixed
     */
    public function getCommonMemberListList()
    {
        $qb=$this->getUserAttributesBasicQueryBuilder();
        $qb->andWhere($qb->expr()->eq("ak.uakMemberListDisplay", 1));
        return $qb->getQuery()->execute();
    }

    /**
     * Method that return all user common attribute are available in profile list
     * @return mixed
     */
    public function getCommonPublicProfileList()
    {
        $qb=$this->getUserAttributesBasicQueryBuilder();
        $qb->andWhere($qb->expr()->eq("ak.uakProfileDisplay", 1));
        return $qb->getQuery()->execute();
    }

    /**
     * Method that return all user common attribute are editable in profile list
     * @return mixed
     */
    public function getCommonEditableInProfileList()
    {
        $qb=$this->getUserAttributesBasicQueryBuilder();
        $qb->andWhere($qb->expr()->eq("ak.uakProfileEdit", 1));
        return $qb->getQuery()->execute();
    }

    /**
     * @param array $groups
     * @return mixed
     */
    public function getGroupsRegistrationList($groups=array())
    {
        $qb=$this->getUserAttributesBasicQueryBuilder($groups);
        $qb->andWhere($qb->expr()->eq("ukug.uakRegisterEdit", 1));
        return $qb->getQuery()->execute();
    }

    /**
     * @param array $groups
     * @return mixed
     */
    public function getGroupsMemberListList($groups=array())
    {
        $qb=$this->getUserAttributesBasicQueryBuilder($groups);
        $qb->andWhere($qb->expr()->eq("ukug.uakMemberListDisplay", 1));
        return $qb->getQuery()->execute();
    }

    /**
     * @param array $groups
     * @return mixed
     */
    public function getGroupsPublicProfileList($groups=array())
    {
        $qb=$this->getUserAttributesBasicQueryBuilder($groups);
        $qb->andWhere($qb->expr()->eq("ukug.uakProfileDisplay", 1));
        return $qb->getQuery()->execute();
    }

    /**
     * @param array $groups
     * @return mixed
     */
    public function getGroupsEditableInProfileList($groups=array())
    {
        $qb=$this->getUserAttributesBasicQueryBuilder($groups);
        $qb->andWhere($qb->expr()->eq("ukug.uakProfileEdit", 1));
        return $qb->getQuery()->execute();
    }
}
