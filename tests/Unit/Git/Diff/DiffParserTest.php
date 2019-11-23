<?php


namespace Kodilab\Deployer\Tests\Unit\Git\Diff;


use Kodilab\Deployer\Changes\Add;
use Kodilab\Deployer\Changes\Change;
use Kodilab\Deployer\Changes\Delete;
use Kodilab\Deployer\Changes\Modify;
use Kodilab\Deployer\Changes\Rename;
use Kodilab\Deployer\Exceptions\DiffEntryStatusUnknown;
use Kodilab\Deployer\Git\Diff\DiffParser;
use Kodilab\Deployer\Support\Collection;
use Kodilab\Deployer\Tests\TestCase;

class DiffParserTest extends TestCase
{
    public function test_an_add_entry_should_returns_a_collection_with_an_add_instance()
    {
        $output = [
            $this->generateDiffEntry(DiffParser::ADDED, $add_path = $this->faker->word . DIRECTORY_SEPARATOR . $this->faker->word),
            $this->generateDiffEntry(DiffParser::MODIFIED, $modified_path = $this->faker->word . DIRECTORY_SEPARATOR . $this->faker->word),
            $this->generateDiffEntry(DiffParser::DELETED, $deleted_path = $this->faker->word . DIRECTORY_SEPARATOR . $this->faker->word),
            $this->generateDiffEntry(DiffParser::RENAMED,
                $rename_source_path = $this->faker->word . DIRECTORY_SEPARATOR . $this->faker->word,
                $rename_destination_path = $this->faker->word . DIRECTORY_SEPARATOR . $this->faker->word,
            ),
        ];

        $diff = (new DiffParser())->parse($output);
        $entries_collection = new Collection();

        /** @var Change $entry */
        foreach ($diff->getEntries() as $entry) {
            $entries_collection->add($entry);
        }

        $added_entry = $entries_collection->where('path', $add_path)->first();
        $modified_entry = $entries_collection->where('path', $modified_path)->first();
        $deleted_entry = $entries_collection->where('path', $deleted_path)->first();
        $renamed_entry_delete = $entries_collection->where('path', $rename_source_path)->first();
        $renamed_entry_add = $entries_collection->where('path', $rename_destination_path)->first();

        $this->assertNotNull($added_entry);
        $this->assertEquals(Add::class, get_class($added_entry));

        $this->assertNotNull($modified_entry);
        $this->assertEquals(Modify::class, get_class($modified_entry));

        $this->assertNotNull($deleted_entry);
        $this->assertEquals(Delete::class, get_class($deleted_entry));

        $this->assertNotNull($renamed_entry_delete);
        $this->assertEquals(Delete::class, get_class($renamed_entry_delete));

        $this->assertNotNull($renamed_entry_add);
        $this->assertEquals(Add::class, get_class($renamed_entry_add));
    }

    public function test_an_unknown_status_entry_should_throw_an_exception()
    {
        $this->expectException(DiffEntryStatusUnknown::class);

        $output = [
            $this->generateDiffEntry('Z', $add_path = $this->faker->word . DIRECTORY_SEPARATOR . $this->faker->word)
        ];

        (new DiffParser())->parse($output);
    }

    public function test_is_hasDependenciesChanged_should_return_true_if_the_file_has_been_modified()
    {
        $output = [
            $this->generateDiffEntry('M', 'composer.lock')
        ];

        $diff = (new DiffParser())->parse($output);
        $this->assertTrue($diff->hasDependenciesChanged());
    }

    /**
     * Generates a output line simulating the diff output
     *
     * @param string $status
     * @param string $source
     * @param string|null $destination
     * @return string
     */
    protected function generateDiffEntry(string $status, string $source, string $destination = null)
    {
        if ($status === DiffParser::RENAMED) {
            $status = $status . '000'; // Diff command returns a three numbers after the status
        }

        $entry_text = $status . "\t" . $source;

        if (!is_null($destination)) {
            $entry_text .= "\t" . $destination . "\n";
        }

        return $entry_text;
    }
}
