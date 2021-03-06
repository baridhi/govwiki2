<?php

namespace GovWiki\EnvironmentBundle;

/**
 * Class GovWikiEnvironmentService
 * @package GovWiki\EnvironmentBundle
 */
abstract class GovWikiEnvironmentService
{
    const STORAGE = 'govwiki_environment.storage';

    const SURVEY_MONKEY_GATEWAY = 'govwiki_enviornment.survey_gateway.survey_monkey';

    const MAX_RANK_MANAGER = 'govwiki_environment.manager.max_ranks';
    const FORMAT_MANAGER = 'govwiki_environment.manager.format';
    const ELECTED_OFFICIAL_MANAGER = 'govwiki_environment.manager.elected_official';
    const GOVERNMENT_MANAGER = 'govwiki_environment.manager.government';
}
