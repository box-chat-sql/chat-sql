<?php
namespace App\Customs;
use BeyondCode\Oracle\Oracle;
use Illuminate\Http\Request;
use App\Models\AskDatabases;
use OpenAI\Client;
use Illuminate\Support\Str;

class customOracle extends Oracle
{
    protected string $connection;

    protected $ip;

    function __construct(protected Client $client,Request $request){
        $this->ip = $request->ip();
        $this->connection = config('ask-database.connection');
    }
    public function getQueryCustom(string $question): string
    {
        $prompt = $this->buildPromptNew($question);
        $query = $this->queryOpenAi($prompt, "\n");
        $query = Str::of($query)
            ->trim()
            ->trim('"');

        $this->ensureQueryIsSafe($query);
        return $query;
    }

    public function buildPromptNew(string $question, string $query = null, string $result = null): string
    {
        $tables = $this->getObjectTable();
        $prompt = (string) view('prompts.query', [
            'question' => $question,
            'tables' => $tables,
            'dialect' => $this->getDialect(),
            'query' => $query,
            'result' => $result,
        ]);
        return rtrim($prompt, PHP_EOL);
    }

    /**
     * @return \Doctrine\DBAL\Schema\Table[]
     */
    protected function getObjectTable(): Object
    {
        return AskDatabases::query()->where('visitor',$this->ip)->pluck('data_convert');
    }
}
