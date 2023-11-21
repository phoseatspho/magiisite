<?php namespace App\Services\Stat;

use App\Services\Service;

use DB;
use Config;

use App\Models\Stat\Stat;
use App\Models\Item\Item;

class StatService extends Service
{
 /**
     * Creates a new stat.
     *
     */
    public function createStat($data)
    {
        DB::beginTransaction();

        try {
            if(!isset($data['name'])) throw new \Exception('Please name the stat');
            if(!isset($data['base'])) throw new \Exception('Please set a default.');
            if(!isset($data['abbreviation'])) throw new \Exception('Please add an abbreviation.');

            $stat = Stat::create($data);

            return $this->commitReturn($stat);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a stat.
     *
     */
    public function updateStat($stat, $data)
    {
        DB::beginTransaction();

        try {

            // check species_ids
            if(isset($data['types']) && $data['types'])
            {
                if ($stat->species) $stat->species()->delete();
                foreach($data['types'] as $key=>$type)
                {
                    if($type == 'species')
                    {
                        if(!isset($data['type_ids'][$key]) || !$data['type_ids'][$key]) throw new \Exception("Please select at least one species.");
                        $stat->species()->create([
                            'species_id' => $data['type_ids'][$key],
                            'type' => 'stat',
                            'type_id' => $stat->id,
                            'is_subtype' => 0
                        ]);
                    }
                    else if($type == 'subtype')
                    {
                        if(!isset($data['type_ids'][$key]) || !$data['type_ids'][$key]) throw new \Exception("Please select at least one subtype.");
                        $stat->species()->create([
                            'species_id' => $data['type_ids'][$key],
                            'type' => 'stat',
                            'type_id' => $stat->id,
                            'is_subtype' => 1
                        ]);
                    }
                }
            }

            $stat->update($data);

            return $this->commitReturn($stat);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a stat.
     *
     */
    public function deleteStat($stat)
    {
        DB::beginTransaction();

        try {
            // Check first if the stat is currently owned or if some other site feature uses it
            if(DB::table('character_stats')->where('stat_id', $stat->id)->exists()) throw new \Exception("A character currently has this stat.");

            $stat->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
