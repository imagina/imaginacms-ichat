<?php

namespace Modules\Ichat\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Isite\Jobs\ProcessSeeds;

class AddValuesProviderInConversationsTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Model::unguard();
    $conversationRepository = app('Modules\Ichat\Repositories\ConversationRepository');
    $providers = \DB::table("notification__providers")->get()->pluck("system_name")->toArray();
    $conversations = \DB::table("ichat__conversations")->get();
    foreach ($conversations as $conversation) {
      if (in_array($conversation->entity_type, $providers) &&
        !is_null($conversation->entity_type) && !is_null($conversation->entity_id)) {
        $data = [
          'entity_id' => null,
          'entity_type' => null,
          'provider_id' => $conversation->entity_id,
          'provider_type' => $conversation->entity_type,
        ];
        $conversationRepository->updateBy($conversation->id, $data);
      }
    }
  }
}