<div class="max-w-5xl mx-auto space-y-6 h-full flex flex-col">

    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Create Gun Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>

            <flux:badge color="blue">Category: Gun</flux:badge>
        </div>
    </flux:card>
    <div class="flex-1 min-h-0 overflow-y-auto px-2">
        <form wire:submit="save" class="space-y-6">
            <flux:accordion>

                {{-- Photos (p_1..p_10) --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-5">Photos</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- Repeat for each slot --}}
                            <div class="space-y-2">
                                <flux:text class="text-gray-600 dark:text-white/60">Upload a Primary Picture for your
                                    post.
                                </flux:text>
                                <flux:file-upload wire:model="primary_photo" label="Primary Photo">
                                    <flux:file-upload.dropzone heading="Drop file or click to browse"
                                        text="JPG, PNG, GIF up to 10MB" inline />
                                </flux:file-upload>
                                @if ($primary_photo)
                                    <flux:file-item :heading="$primary_photo->getClientOriginalName()"
                                        :image="$primary_photo->temporaryUrl()" :size="$primary_photo->getSize()"
                                        class="flex items-center">
                                        <x-slot name="actions">
                                            <flux:file-item.remove wire:click="removePrimaryPhoto" />
                                        </x-slot>
                                    </flux:file-item>
                                @endif

                                {{-- <flux:error name="primary_photo" /> --}}
                            </div>

                            <div class="space-y-2">
                                <flux:text class="text-gray-600 dark:text-white/60">You can also upload additional
                                    photos
                                    (up to 9).
                                </flux:text>
                                <flux:file-upload wire:model="other_photos" multiple label="Other Photos">
                                    <flux:file-upload.dropzone heading="Drop file or click to browse"
                                        text="JPG, PNG, GIF up to 10MB" inline />
                                </flux:file-upload>
                                @if ($other_photos)
                                    @foreach ($other_photos as $index => $photo)
                                        <flux:file-item :heading="$photo->getClientOriginalName()"
                                            :image="$photo->temporaryUrl()" :size="$photo->getSize()"
                                            class="flex items-center">
                                            <x-slot name="actions">
                                                <flux:file-item.remove
                                                    wire:click="removeOtherPhoto({{ $index }})" />
                                            </x-slot>
                                        </flux:file-item>
                                    @endforeach

                                @endif
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- Photos (p_1..p_10) --}}


                {{-- Listing --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-5">Listing details</flux:accordion.heading>
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
                                <flux:label>Title <span class="text-red-500"> *</span></flux:label>
                                <flux:input wire:model="title" placeholder="e.g., Glock 19 Gen 5 w/ extras" />
                                <flux:error name="title" />
                            </flux:field>

                            <flux:field class="md:col-span-2">
                                <flux:label>Description <span class="text-red-500"> *</span></flux:label>
                                <flux:textarea wire:model="description" rows="6"
                                    placeholder="Condition, inclusions, history, meetup/shipping notes..." />
                                <flux:error name="description" />
                            </flux:field>

                            <flux:field>
                                <flux:label>
                                    General condition
                                    <span class="text-red-500"> *</span>
                                </flux:label>

                                <flux:select wire:model="post_condition">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->postConditionOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="post_condition" />
                            </flux:field>

                            @if ($this->listing_type === 'sell')
                                <flux:field>
                                    <flux:label>
                                        Price
                                        <span class="text-red-500"> *</span>
                                    </flux:label>
                                    <flux:input type="number" step="0.01" wire:model="price" placeholder="0.00" />
                                    <flux:error name="price" />
                                </flux:field>
                            @elseif($this->listing_type === 'buy')
                                <flux:field>
                                    <flux:label>
                                        Budget range (min)
                                        <span class="text-red-500"> *</span>
                                    </flux:label>
                                    <flux:input type="number" step="0.01" wire:model="buy_min_price"
                                        placeholder="0.00" />
                                    <flux:error name="buy_min_price" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>
                                        Budget range (max)
                                        <span class="text-red-500"> *</span>
                                    </flux:label>
                                    <flux:input type="number" step="0.01" wire:model="buy_max_price"
                                        placeholder="0.00" />
                                    <flux:error name="buy_max_price" />
                                </flux:field>
                            @endif





                            <div class="flex items-center gap-3 mt-7">
                                <flux:switch wire:model="is_negotiable" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Negotiable</div>
                                    <div class="text-xs text-zinc-500">Allow offers on this listing.</div>
                                </div>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- Listing --}}


                {{-- 2) Gun identification --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-5">Gun identification</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Manufacturer<span class="text-red-500"> *</span></flux:label>
                                <flux:input wire:model="manufacturer" placeholder="e.g., Glock" />
                                <flux:error name="manufacturer" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Model<span class="text-red-500"> *</span></flux:label>
                                <flux:input wire:model="model" placeholder="e.g., 19" />
                                <flux:error name="model" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Variant</flux:label>
                                <flux:input wire:model="variant" placeholder="e.g., Gen 5 / MOS" />
                                <flux:error name="variant" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Series</flux:label>
                                <flux:input wire:model="series" placeholder="e.g., Standard" />
                                <flux:error name="series" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Country of origin</flux:label>
                                <flux:input wire:model="country_of_origin" placeholder="e.g., USA" />
                                <flux:error name="country_of_origin" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 2) Gun identification --}}

                {{-- 3) Classification --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-5">Classification</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Platform <span class="text-red-500"> *</span></flux:label>
                                <flux:select wire:model="platform">
                                    <option value="">— Optional —</option>
                                    @foreach ($this->platformOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </flux:select>
                                <flux:error name="platform" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Type<span class="text-red-500"> *</span></flux:label>
                                <flux:input wire:model="type" placeholder="e.g., 1911, AR-15" />
                                <flux:error name="type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Action <span class="text-red-500"> *</span></flux:label>
                                <flux:input wire:model="action" placeholder="e.g., semi-auto, bolt, pump" />
                                <flux:error name="action" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 3) Classification --}}

                {{-- 4) Core specs --}}
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-5">Core specs</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <flux:field>
                                <flux:label>Caliber <span class="text-red-500"> *</span></flux:label>
                                <flux:input wire:model="caliber" placeholder="e.g., 9mm" />
                                <flux:error name="caliber" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Capacity</flux:label>
                                <flux:input type="number" wire:model="capacity" placeholder="e.g., 15" />
                                <flux:error name="capacity" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Barrel length</flux:label>
                                <flux:input type="number" step="0.01" wire:model="barrel_length"
                                    placeholder="e.g., 4.02" />
                                <flux:error name="barrel_length" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Overall length</flux:label>
                                <flux:input type="number" step="0.01" wire:model="overall_length"
                                    placeholder="e.g., 7.28" />
                                <flux:error name="overall_length" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Height</flux:label>
                                <flux:input type="number" step="0.01" wire:model="height"
                                    placeholder="e.g., 5.43" />
                                <flux:error name="height" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Width</flux:label>
                                <flux:input type="number" step="0.01" wire:model="width"
                                    placeholder="e.g., 1.18" />
                                <flux:error name="width" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Weight</flux:label>
                                <flux:input type="number" step="0.001" wire:model="weight"
                                    placeholder="e.g., 0.75" />
                                <flux:error name="weight" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Weight unit</flux:label>
                                <flux:select wire:model="weight_unit">
                                    <option value="kg">kg</option>
                                    <option value="lb">lb</option>
                                </flux:select>
                                <flux:error name="weight_unit" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 4) Core specs --}}

                {{-- 5) Materials & finish --}}
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-5">Materials & finish</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Frame material</flux:label>
                                <flux:input wire:model="frame_material" placeholder="e.g., Polymer" />
                                <flux:error name="frame_material" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Slide material</flux:label>
                                <flux:input wire:model="slide_material" placeholder="e.g., Steel" />
                                <flux:error name="slide_material" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Barrel material</flux:label>
                                <flux:input wire:model="barrel_material" placeholder="e.g., Steel" />
                                <flux:error name="barrel_material" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Finish</flux:label>
                                <flux:input wire:model="finish" placeholder="e.g., Cerakote" />
                                <flux:error name="finish" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Color</flux:label>
                                <flux:input wire:model="color" placeholder="e.g., Black" />
                                <flux:error name="color" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Grip type</flux:label>
                                <flux:input wire:model="grip_type" placeholder="e.g., Polymer" />
                                <flux:error name="grip_type" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Stock type</flux:label>
                                <flux:input wire:model="stock_type" placeholder="e.g., Fixed" />
                                <flux:error name="stock_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Handguard type</flux:label>
                                <flux:input wire:model="handguard_type" placeholder="e.g., M-LOK" />
                                <flux:error name="handguard_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Rail type</flux:label>
                                <flux:select wire:model="rail_type">
                                    <option value="">— Optional —</option>
                                    <option value="picatinny">Picatinny</option>
                                    <option value="mlok">M-LOK</option>
                                    <option value="keymod">KeyMod</option>
                                    <option value="none">None</option>
                                </flux:select>
                                <flux:error name="rail_type" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 5) Materials & finish --}}

                {{-- 6) Sights & optics --}}
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-5">Sights & optics</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Sight type</flux:label>
                                <flux:input wire:model="sight_type" placeholder="e.g., Iron sights" />
                                <flux:error name="sight_type" />
                            </flux:field>

                            <div class="flex items-center gap-3 mt-7">
                                <flux:switch wire:model="optic_ready" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Optic ready</div>
                                    <div class="text-xs text-zinc-500">Slide/cut ready for optics.</div>
                                </div>
                            </div>

                            <flux:field>
                                <flux:label>Optic mount pattern</flux:label>
                                <flux:input wire:model="optic_mount_pattern" placeholder="e.g., RMR, MOS" />
                                <flux:error name="optic_mount_pattern" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 6) Sights & optics --}}

                {{-- 7) Barrel & muzzle --}}
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-5">Barrel & muzzle</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center gap-3 mt-1">
                                <flux:switch wire:model="threaded_barrel" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Threaded barrel</div>
                                    <div class="text-xs text-zinc-500">Barrel has threads.</div>
                                </div>
                            </div>

                            <flux:field>
                                <flux:label>Thread pitch</flux:label>
                                <flux:input wire:model="thread_pitch" placeholder="e.g., 1/2x28" />
                                <flux:error name="thread_pitch" />
                            </flux:field>

                            <div class="flex items-center gap-3 mt-1">
                                <flux:switch wire:model="muzzle_device_included" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Muzzle device included</div>
                                    <div class="text-xs text-zinc-500">Comp/flash hider included.</div>
                                </div>
                            </div>

                            <flux:field class="md:col-span-3">
                                <flux:label>Muzzle device type</flux:label>
                                <flux:input wire:model="muzzle_device_type"
                                    placeholder="e.g., compensator, flash hider" />
                                <flux:error name="muzzle_device_type" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 7) Barrel & muzzle --}}

                {{-- 8) Safety & trigger --}}
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-5">Safety & trigger</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <flux:field class="md:col-span-2">
                                <flux:label>Trigger type</flux:label>
                                <flux:input wire:model="trigger_type" placeholder="e.g., Striker" />
                                <flux:error name="trigger_type" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Trigger pull</flux:label>
                                <flux:input type="number" step="0.01" wire:model="trigger_pull"
                                    placeholder="e.g., 5.5" />
                                <flux:error name="trigger_pull" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Unit</flux:label>
                                <flux:select wire:model="trigger_pull_unit">
                                    <option value="lb">lb</option>
                                    <option value="kg">kg</option>
                                </flux:select>
                                <flux:error name="trigger_pull_unit" />
                            </flux:field>

                            <div class="flex items-center gap-3 mt-1">
                                <flux:switch wire:model="has_manual_safety" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Manual safety</div>
                                    <div class="text-xs text-zinc-500">Has a manual safety.</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 mt-1">
                                <flux:switch wire:model="has_firing_pin_safety" />
                                <div class="space-y-0.5">
                                    <div class="text-sm font-medium">Firing pin safety</div>
                                    <div class="text-xs text-zinc-500">Has firing pin safety.</div>
                                </div>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 8) Safety & trigger --}}

                {{-- 9) Metadata & inclusions --}}
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-5">Metadata & inclusions</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>SKU</flux:label>
                                <flux:input wire:model="sku" placeholder="e.g., GLK-19-G5" />
                                <flux:error name="sku" />
                            </flux:field>
                            <flux:field>
                                <flux:label>UPC</flux:label>
                                <flux:input wire:model="upc" placeholder="e.g., 764503035192" />
                                <flux:error name="upc" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Gun condition</flux:label>
                                <flux:select wire:model="gun_condition">
                                    <option value="">— Optional —</option>
                                    <option value="new">New</option>
                                    <option value="like_new">Like New</option>
                                    <option value="used">Used</option>
                                    <option value="refurbished">Refurbished</option>
                                    <option value="for_parts">For Parts</option>
                                </flux:select>
                                <flux:error name="gun_condition" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Round count estimate</flux:label>
                                <flux:input type="number" wire:model="round_count_estimate"
                                    placeholder="e.g., 500" />
                                <flux:error name="round_count_estimate" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Included magazines</flux:label>
                                <flux:input type="number" wire:model="included_magazines" placeholder="e.g., 2" />
                                <flux:error name="included_magazines" />
                            </flux:field>

                            <div class="flex flex-wrap items-center gap-4 md:col-span-3">
                                <label class="flex items-center gap-2">
                                    <flux:checkbox wire:model="has_box" />
                                    <span class="text-sm">Has box</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <flux:checkbox wire:model="has_receipt" />
                                    <span class="text-sm">Has receipt</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <flux:checkbox wire:model="has_documents" />
                                    <span class="text-sm">Has documents (flag only)</span>
                                </label>
                            </div>

                            <flux:field class="md:col-span-3">
                                <flux:label>Document notes</flux:label>
                                <flux:textarea wire:model="document_notes" rows="3"
                                    placeholder="e.g., Original box, manual included" />
                                <flux:error name="document_notes" />
                            </flux:field>

                            <flux:field class="md:col-span-3">
                                <flux:label>Included accessories</flux:label>
                                <flux:textarea wire:model="included_accessories" rows="3"
                                    placeholder="case, sling, optic, etc." />
                                <flux:error name="included_accessories" />
                            </flux:field>

                            <flux:field class="md:col-span-3">
                                <flux:label>Extra notes</flux:label>
                                <flux:textarea wire:model="notes" rows="3"
                                    placeholder="e.g., Custom trigger work" />
                                <flux:error name="notes" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>
                {{-- 9) Metadata & inclusions --}}

            </flux:accordion>

            <div class="flex items-center justify-end gap-3">

                <flux:button type="submit" variant="primary" class="hover:cursor-pointer">
                    Submit for review
                </flux:button>

                <flux:button href="{{ route('posts') }}" type="button" variant="ghost">← Back </flux:button>

            </div>
        </form>
    </div>

</div>
