<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengajuanItem>
 */
class PengajuanItemFactory extends Factory
{
    protected $model = PengajuanItem::class;

    public function definition(): array
    {
        $mulai = $this->faker->dateTimeBetween('+1 week', '+2 months');

        return [
            'pengajuan_id' => Pengajuan::factory(),
            'assetable_type' => Ruangan::class,
            'assetable_id' => Ruangan::inRandomOrder()->value('id') ?? Ruangan::factory(),
            'jumlah' => 1,
            'mulai_at' => $mulai,
            'selesai_at' => (clone $mulai)->modify('+4 hours'),
            'status_item' => PengajuanItem::STATUS_DIAJUKAN,
        ];
    }

    public function untukAset(Ruangan|Barang $aset, int $jumlah = 1): static
    {
        return $this->state(fn () => [
            'assetable_type' => $aset::class,
            'assetable_id' => $aset->id,
            'jumlah' => $jumlah,
        ]);
    }

    public function berstatus(string $status): static
    {
        return $this->state(fn () => ['status_item' => $status]);
    }
}
