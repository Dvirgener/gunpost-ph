<div class="max-w-5xl mx-auto space-y-6 flex flex-col h-full">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Edit Airsoft Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>
            <flux:badge color="blue">Category: Airsoft</flux:badge>
        </div>
    </flux:card>

    <div class="flex-1 h-min-0 overflow-y-scroll px-2">
        <form wire:submit="save" class="space-y-6">
            <flux:accordion>
                <!-- Photos -->
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Photos</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <flux:text class="text-xs text-red-500">Replace the Primary Photo if needed</flux:text>
                                <flux:file-upload wire:model="primary_photo" label="Primary Photo">
                                    <flux:file-upload.dropzone heading="Drop file or click to browse"
                                        text="JPG, PNG, GIF up to 10MB" inline />
                                </flux:file-upload>
                                @if ($primary_photo)
                                    <flux:file-item :heading="$primary_photo->getClientOriginalName()"
                                        :image="$primary_photo->temporaryUrl()" :size="$primary_photo->getSize()">
                                        <x-slot name="actions">
                                            <flux:file-item.remove wire:click="removePrimaryPhoto" />
                                        </x-slot>
                                    </flux:file-item>
                                @endif

                                <flux:text>Current upload:</flux:text>
                                <div
                                    class="w-full py-1 rounded-md border shadow flex justify-between items-center px-3 dark:bg-stone-800 dark:border-stone-700/60 dark:shadow-stone-900/50">
                                    <img src="{{ asset('storage/' . $this->uploaded_primary_photo) }}"
                                        alt="Photo 1 preview" class="object-cover w-15 h-15" />

                                </div>
                            </div>

                            <div class="space-y-2">
                                <flux:text class="text-xs text-red-500">Add or replace additional photos</flux:text>
                                <flux:file-upload wire:model="other_photos" multiple label="Other Photos">
                                    <flux:file-upload.dropzone heading="Drop file or click to browse"
                                        text="JPG, PNG, GIF up to 10MB" inline />
                                </flux:file-upload>

                                <flux:text>Current upload:</flux:text>
                                @foreach ($uploaded_other_photos as $index => $photo)
                                    @if ($photo)
                                        <div
                                            class="w-full py-1 rounded-md border shadow flex justify-between items-center px-3 dark:bg-stone-800 dark:border-stone-700/60 dark:shadow-stone-900/50">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Photo 1 preview"
                                                class="object-cover w-15 h-15" />

                                            <flux:button icon="x-mark" class="hover:cursor-pointer"
                                                wire:click="removePhoto({{ $index }})"></flux:button>

                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Listing details (same as create) -->
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Listing details</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Listing type <span class="text-red-500">*</span></flux:label>
                                <flux:select wire:model.live="listing_type">
                                    @foreach ($this->listingTypeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="listing_type" />
                            </flux:field>

                            <flux:field class="md:col-span-2">
                                <flux:label>Title <span class="text-red-500">*</span></flux:label>
                                <flux:input wire:model.live="title" />
                                <flux:error name="title" />
                            </flux:field>

                            <flux:field class="md:col-span-2">
                                <flux:label>Description <span class="text-red-500">*</span></flux:label>
                                <flux:textarea wire:model.live="description" rows="5" />
                                <flux:error name="description" />
                            </flux:field>

                            <flux:field>
                                <flux:label>General condition <span class="text-red-500">*</span></flux:label>
                                <flux:select wire:model.live="post_condition">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->postConditionOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="post_condition" />
                            </flux:field>

                            @if ($this->listing_type === 'sell')
                                <flux:field>
                                    <flux:label>Price <span class="text-red-500">*</span></flux:label>
                                    <flux:input type="number" step="0.01" wire:model.live="price" />
                                    <flux:error name="price" />
                                </flux:field>
                            @elseif($this->listing_type === 'buy')
                                <flux:field>
                                    <flux:label>Budget min <span class="text-red-500">*</span></flux:label>
                                    <flux:input type="number" step="0.01" wire:model.live="buy_min_price" />
                                    <flux:error name="buy_min_price" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>Budget max <span class="text-red-500">*</span></flux:label>
                                    <flux:input type="number" step="0.01" wire:model.live="buy_max_price" />
                                    <flux:error name="buy_max_price" />
                                </flux:field>
                            @endif

                            <div class="flex items-center gap-3 mt-7">
                                <flux:switch wire:model.live="is_negotiable" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Negotiable</div>
                                </div>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Airsoft fields (keep same as create for consistency) -->
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Airsoft identification</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Brand</flux:label>
                                <flux:input wire:model.live="brand" />
                                <flux:error name="brand" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Model</flux:label>
                                <flux:input wire:model.live="model" />
                                <flux:error name="model" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Series</flux:label>
                                <flux:input wire:model.live="series" />
                                <flux:error name="series" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Classification, performance, etc. -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Classification</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Platform</flux:label>
                                <flux:select wire:model.live="platform">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->platformOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="platform" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Power source</flux:label>
                                <flux:select wire:model.live="power_source">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->powerSourceOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="power_source" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Compatibility platform</flux:label>
                                <flux:input wire:model.live="compatibility_platform" />
                                <flux:error name="compatibility_platform" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Gearbox version</flux:label>
                                <flux:input wire:model.live="gearbox_version" />
                                <flux:error name="gearbox_version" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Performance</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>FPS</flux:label>
                                <flux:input type="number" wire:model.live="fps" />
                                <flux:error name="fps" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Joule</flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="joule" />
                                <flux:error name="joule" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Build</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Color</flux:label>
                                <flux:input wire:model.live="color" />
                                <flux:error name="color" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Body material</flux:label>
                                <flux:input wire:model.live="body_material" />
                                <flux:error name="body_material" />
                            </flux:field>
                            <div class="flex items-center gap-3 mt-1">
                                <flux:switch wire:model.live="metal_body" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Metal body</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-1">
                                <flux:switch wire:model.live="blowback" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Blowback</div>
                                </div>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Power / Magazine</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Battery type</flux:label>
                                <flux:input wire:model.live="battery_type" />
                                <flux:error name="battery_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Battery connector</flux:label>
                                <flux:input wire:model.live="battery_connector" />
                                <flux:error name="battery_connector" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Gas type</flux:label>
                                <flux:input wire:model.live="gas_type" />
                                <flux:error name="gas_type" />
                            </flux:field>

                            <div class="flex items-center gap-3 mt-1">
                                <flux:switch wire:model.live="includes_magazines" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Includes magazines</div>
                                </div>
                            </div>
                            <flux:field>
                                <flux:label>Magazine count</flux:label>
                                <flux:input type="number" wire:model.live="magazine_count" />
                                <flux:error name="magazine_count" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Magazine type</flux:label>
                                <flux:input wire:model.live="magazine_type" />
                                <flux:error name="magazine_type" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Package & Extras</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:field class="md:col-span-3">
                            <flux:label>Package includes</flux:label>
                            <flux:textarea wire:model.live="package_includes" rows="3" />
                            <flux:error name="package_includes" />
                        </flux:field>
                    </flux:accordion.content>
                </flux:accordion.item>

                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Notes</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:field class="md:col-span-3">
                            <flux:label>Notes</flux:label>
                            <flux:textarea wire:model.live="notes" rows="4" />
                            <flux:error name="notes" />
                        </flux:field>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Update Post</flux:button>
                <flux:button
                    href="{{ route('posts.view.category.index', ['post' => $post, 'category' => $post->category]) }}"
                    variant="ghost">← Back </flux:button>
            </div>
        </form>
    </div>


</div>
