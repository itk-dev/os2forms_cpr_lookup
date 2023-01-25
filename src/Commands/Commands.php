<?php

namespace Drupal\os2forms_cpr_lookup\Commands;

use Drupal\os2forms_cpr_lookup\Service\CprServiceInterface;
use Drush\Commands\DrushCommands;
use Symfony\Component\Yaml\Yaml;

/**
 * Drush commands for os2forms_cpr_lookup.
 */
final class Commands extends DrushCommands {

  /**
   * The CPR service.
   */
  private CprServiceInterface $cprService;

  /**
   * Constructor.
   */
  public function __construct(CprServiceInterface $cprService) {
    $this->cprService = $cprService;
  }

  /**
   * Look up CPR.
   *
   * @param string $cpr
   *   The cpr.
   *
   * @command os2forms_cpr_lookup:search
   * @usage os2forms_cpr_lookup:search --help
   */
  public function search(string $cpr) {
    $result = $this->cprService->search($cpr);

    $this->writeln(Yaml::dump($result->toArray()));
  }

}
