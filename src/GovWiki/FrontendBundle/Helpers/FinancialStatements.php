<?php
declare(strict_types=1);

namespace GovWiki\FrontendBundle\Helpers;

/**
 * @author Ivan Glushakov <ivan.glushakov@sibers.com>
 */
final class FinancialStatements
{
    const KEY_TOTAL_GOV_FUNDS = 'totalfunds';
    const KEY_OTHER_FUNDS     = 'otherfunds';
    const KEY_GENERAL_FUNDS   = 'genfund';

    /**
     * @return array
     */
    public static function getOptionsFilter():array
    {
        return [
            'Total Gov. Funds' => self::KEY_TOTAL_GOV_FUNDS,
            'Other Funds'      => self::KEY_OTHER_FUNDS,
            'General Funds'    => self::KEY_GENERAL_FUNDS,
        ];
    }
}
