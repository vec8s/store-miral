<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Identity\Models\User;
use App\Domains\Media\Enums\MediaCollection;
use App\Domains\Media\Enums\MediaDisk;
use App\Domains\Media\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            "mediable_type" => User::class,
            "mediable_id" => User::factory(),
            "collection" => MediaCollection::Default,
            "disk" => MediaDisk::Public,
            "path" => "media/" . $this->faker->uuid() . ".jpg",
            "filename" => $this->faker->word() . ".jpg",
            "mime_type" => "image/jpeg",
            "size" => $this->faker->numberBetween(10000, 5000000),
            "width" => $this->faker->numberBetween(200, 4000),
            "height" => $this->faker->numberBetween(200, 4000),
            "responsive_images" => null,
            "alt_text" => $this->faker->words(3, true),
            "sort_order" => $this->faker->numberBetween(0, 100),
            "uploaded_by_id" => User::factory(),
        ];
    }

    public function image(): static
    {
        return $this->state(fn () => [
            "mime_type" => "image/jpeg",
            "filename" => $this->faker->word() . ".jpg",
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            "mime_type" => "video/mp4",
            "filename" => $this->faker->word() . ".mp4",
            "width" => null,
            "height" => null,
        ]);
    }

    public function document(): static
    {
        return $this->state(fn () => [
            "mime_type" => "application/pdf",
            "filename" => $this->faker->word() . ".pdf",
            "width" => null,
            "height" => null,
        ]);
    }

    public function inCollection(MediaCollection $collection): static
    {
        return $this->state(fn () => ["collection" => $collection]);
    }

    public function onDisk(MediaDisk $disk): static
    {
        return $this->state(fn () => ["disk" => $disk]);
    }

    public function forModel(\Illuminate\Database\Eloquent\Model $model): static
    {
        return $this->state(fn () => [
            "mediable_type" => $model->getMorphClass(),
            "mediable_id" => $model->getKey(),
        ]);
    }
}
