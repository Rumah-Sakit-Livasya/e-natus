<?php

namespace Database\Seeders;

use App\Models\Aset;
use App\Models\Lander;
use App\Models\Template;
use Illuminate\Database\Seeder;

class AsetSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure prerequisites exist
        $landers = Lander::query()->get();
        if ($landers->isEmpty()) {
            $landers = collect([
                Lander::query()->firstOrCreate(['code' => 'LV'], ['name' => 'Ibu Lia Vallini']),
                Lander::query()->firstOrCreate(['code' => 'LA'], ['name' => 'Ibu Luci']),
                Lander::query()->firstOrCreate(['code' => 'NVM'], ['name' => 'Natus']),
            ]);
        }

        $templates = Template::query()->get();
        if ($templates->isEmpty()) {
            $this->call(TemplateSeeders::class);
            $templates = Template::query()->get();
        }

        $templateByCode = $templates->keyBy('code');

        $assetBlueprints = [
            // template_code, name, harga_sewa
            ['template_code' => 'TNS', 'custom_name' => 'Tensimeter Digital', 'harga_sewa' => 150000],
            ['template_code' => 'STT', 'custom_name' => 'Stetoskop', 'harga_sewa' => 50000],
            ['template_code' => 'ECG', 'custom_name' => 'ECG Portable', 'harga_sewa' => 350000],
            ['template_code' => 'MEJA', 'custom_name' => 'Meja Pemeriksaan', 'harga_sewa' => 100000],
            ['template_code' => 'KRS', 'custom_name' => 'Kursi Tunggu', 'harga_sewa' => 40000],
        ];

        foreach ($landers as $lander) {
            foreach ($assetBlueprints as $blueprint) {
                $template = $templateByCode->get($blueprint['template_code']);
                if (! $template) {
                    continue;
                }

                $exists = Aset::query()->where('lander_id', $lander->id)
                    ->where('template_id', $template->id)
                    ->where('custom_name', $blueprint['custom_name'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $asset = new Aset([
                    'template_id' => $template->id,
                    'lander_id' => $lander->id,
                    'custom_name' => $blueprint['custom_name'],
                    'condition' => 'baik',
                    'harga_sewa' => $blueprint['harga_sewa'],
                    'status' => 'available',
                ]);

                // Ensure code generation uses actual lander/template code
                $asset->setRelation('lander', $lander);
                $asset->setRelation('template', $template);

                $asset->save();
            }
        }
    }
}
