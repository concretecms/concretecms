<?php
namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Closure;
use Concrete\Core\User\UserInfoRepository;
use SimpleXMLElement;

abstract class AbstractPageStructureRoutine extends AbstractRoutine
{
    private $resolvedUserNames = [];

    /**
     * @deprecated use sortElementsByPath()
     */
    public static function setupPageNodeOrder($pageNodeA, $pageNodeB)
    {
        $pathA = (string) $pageNodeA['path'];
        $pathB = (string) $pageNodeB['path'];
        $numA = count(explode('/', $pathA));
        $numB = count(explode('/', $pathB));
        if ($numA == $numB) {
            if (intval($pageNodeA->originalPos) < intval($pageNodeB->originalPos)) {
                return -1;
            } else {
                if (intval($pageNodeA->originalPos) > intval($pageNodeB->originalPos)) {
                    return 1;
                } else {
                    return 0;
                }
            }
        } else {
            return ($numA < $numB) ? -1 : 1;
        }
    }

    /**
     * @param \SimpleXMLElement[] $elements
     */
    protected function sortElementsByPath(array $elements, ?Closure $customComparer = null)
    {
        $sortedElements = $elements;
        usort($sortedElements, static function (SimpleXMLElement $a, SimpleXMLElement $b) use (&$elements, $customComparer) {
            if ($customComparer !== null) {
                $cmp = $customComparer($a, $b);
                if ($cmp) {
                    return $cmp;
                }
            }
            $pathA = trim((string) $a['path'], '/');
            $pathB = trim((string) $b['path'], '/');
            $numA = $pathA === '' ? -1 : substr_count($pathA, '/');
            $numB = $pathB === '' ? -1 : substr_count($pathB, '/');
            if ($numA !== $numB) {
                return $numA - $numB;
            }
            $indexA = array_search($a, $elements, true);
            $indexB = array_search($b, $elements, true);

            return $indexA - $indexB;
        });

        return array_values($sortedElements);
    }

    /**
     * @param string|\SimpleXMLElement|null $userName
     */
    protected function resolveUserName($userName)
    {
        $userName = (string) $userName;
        if ($userName === '') {
            return USER_SUPER_ID;
        }
        if (isset($this->resolvedUserNames[$userName])) {
            return $this->resolvedUserNames[$userName];
        }
        $userInfo = app(UserInfoRepository::class)->getByName($userName);
        $userID = $userInfo === null ? USER_SUPER_ID : (int) $userInfo->getUserID();
        $this->resolvedUserNames[$userName] = $userID;

        return $userID;
    }
}
