<?php
/**
 * This file is part of the PlusClouds.Account library.
 *
 * (c) Semih Turna <semih.turna@plusclouds.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NextDeveloper\I18n\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class UserMigrationCommand.
 *
 * @package PlusClouds\Account\Console\Commands
 */
class GenerateLanguageFiles extends Command {
    /**
     * @var string
     */
    protected $signature = 'nextdeveloper:generate-language-files';

    /**
     * @var string
     */
    protected $description = 'Generates language file and saves it to related folder';

    public function handle() {
        $this->line("Starting to generate files");

        \NextDeveloper\I18n\Services\LanguageGeneratorService::generate();

        $this->line("Finished generation. Enjoy!");
    }
}
