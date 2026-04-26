<div class="max-w-5xl mx-auto space-y-6 flex flex-col h-full">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Create Others Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>
            <flux:badge color="blue">Category: Others</flux:badge>
        </div>
    </flux:card>

    <div class="flex-1 min-h-0 overflow-y-scroll px-2">
        <form wire:submit="save" class="space-y-6">
            <flux:accordion>
                <!-- Photos -->
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Photos</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <flux:text>Upload Primary Photo</flux:text>
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
                            </div>
                            <div class="space-y-2">
                                <flux:text>Upload additional photos (up to 9)</flux:text>
                                <flux:file-upload wire:model="other_photos" multiple label="Other Photos">
                                    <flux:file-upload.dropzone heading="Drop file or click to browse"
                                        text="JPG, PNG, GIF up to 10MB" inline />
                                </flux:file-upload>
                                @if ($other_photos)
                                    @foreach ($other_photos as $index => $photo)
                                        <flux:file-item :heading="$photo->getClientOriginalName()"
                                            :image="$photo->temporaryUrl()" :size="$photo->getSize()">
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

                <!-- Listing -->
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
                                <flux:input wire:model="title" placeholder="e.g., Custom Bowie Knife, Samurai Sword" />
                                <flux:error name="title" />
                            </flux:field>

                            <flux:field class="md:col-span-2">
                                <flux:label>Description <span class="text-red-500">*</span></flux:label>
                                <flux:textarea wire:model="description" rows="5"
                                    placeholder="Condition, features, history, shipping notes" />
                                <flux:error name="description" />
                            </flux:field>

                            <flux:field>
                                <flux:label>General condition <span class="text-red-500">*</span></flux:label>
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
                                    <flux:label>Price <span class="text-red-500">*</span></flux:label>
                                    <flux:input type="number" step="0.01" wire:model="price" placeholder="0.00" />
                                    <flux:error name="price" />
                                </flux:field>
                            @elseif($this->listing_type === 'buy')
                                <flux:field>
                                    <flux:label>Budget min <span class="text-red-500">*</span></flux:label>
                                    <flux:input type="number" step="0.01" wire:model="buy_min_price"
                                        placeholder="0.00" />
                                    <flux:error name="buy_min_price" />
                                </flux:field>

                                <flux:field>
                                    <flux:label>Budget max <span class="text-red-500">*</span></flux:label>
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

                <!-- Weapon Classification -->
                <flux:accordion.item expanded>
                    <flux:accordion.heading class="text-blue-500! mb-3">Weapon classification</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Weapon type</flux:label>
                                <flux:select wire:model="weapon_type">
                                    <option value="">— Optional —</option>
                                    <option value="knife">Knife</option>
                                    <option value="sword">Sword</option>
                                    <option value="machete">Machete</option>
                                    <option value="axe">Axe</option>
                                    <option value="tomahawk">Tomahawk</option>
                                    <option value="baton">Baton</option>
                                    <option value="stick">Stick</option>
                                    <option value="tonfa">Tonfa</option>
                                    <option value="spear">Spear</option>
                                    <option value="other">Other</option>
                                </flux:select>
                                <flux:error name="weapon_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Subcategory</flux:label>
                                <flux:input wire:model="subcategory" placeholder="e.g., karambit, bowie, katana" />
                                <flux:error name="subcategory" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Intended use</flux:label>
                                <flux:input wire:model="intended_use" placeholder="e.g., utility, training, display" />
                                <flux:error name="intended_use" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Identity -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Identity</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Brand</flux:label>
                                <flux:input wire:model="brand" placeholder="e.g., Benchmade, Cold Steel" />
                                <flux:error name="brand" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Model</flux:label>
                                <flux:input wire:model="model" placeholder="e.g., Bushcrafter 162" />
                                <flux:error name="model" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Variant</flux:label>
                                <flux:input wire:model="variant" placeholder="e.g., Tanto, Drop Point" />
                                <flux:error name="variant" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Country of origin</flux:label>
                                <flux:input wire:model="country_of_origin" placeholder="e.g., USA, Japan" />
                                <flux:error name="country_of_origin" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Blade / Head Specs -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Blade / Head specifications
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Blade type</flux:label>
                                <flux:input wire:model="blade_type" placeholder="e.g., fixed, folding, serrated" />
                                <flux:error name="blade_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Edge type</flux:label>
                                <flux:select wire:model="edge_type">
                                    <option value="">— Optional —</option>
                                    <option value="plain">Plain</option>
                                    <option value="serrated">Serrated</option>
                                    <option value="combo">Combo</option>
                                </flux:select>
                                <flux:error name="edge_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Steel type</flux:label>
                                <flux:input wire:model="steel_type" placeholder="e.g., 440C, D2, VG-10" />
                                <flux:error name="steel_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Finish</flux:label>
                                <flux:input wire:model="finish" placeholder="e.g., stonewash, satin" />
                                <flux:error name="finish" />
                            </flux:field>

                            <div class="flex items-center gap-2">
                                <flux:switch wire:model="full_tang" />
                                <flux:label>Full tang</flux:label>
                            </div>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Dimensions -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Dimensions</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Overall length</flux:label>
                                <flux:input type="number" step="0.01" wire:model="overall_length"
                                    placeholder="0.00" />
                                <flux:error name="overall_length" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Blade length</flux:label>
                                <flux:input type="number" step="0.01" wire:model="blade_length"
                                    placeholder="0.00" />
                                <flux:error name="blade_length" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Head length</flux:label>
                                <flux:input type="number" step="0.01" wire:model="head_length"
                                    placeholder="0.00" />
                                <flux:error name="head_length" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Handle length</flux:label>
                                <flux:input type="number" step="0.01" wire:model="handle_length"
                                    placeholder="0.00" />
                                <flux:error name="handle_length" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Length unit</flux:label>
                                <flux:select wire:model="length_unit">
                                    <option value="cm">cm</option>
                                    <option value="in">in</option>
                                </flux:select>
                                <flux:error name="length_unit" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Weight -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Weight</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Weight</flux:label>
                                <flux:input type="number" step="0.001" wire:model="weight" placeholder="0.000" />
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

                <!-- Handle / Grip -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Handle / Grip</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <flux:field>
                                <flux:label>Handle material</flux:label>
                                <flux:input wire:model="handle_material" placeholder="e.g., g10, micarta, wood" />
                                <flux:error name="handle_material" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Handle color</flux:label>
                                <flux:input wire:model="handle_color" placeholder="e.g., black, brown" />
                                <flux:error name="handle_color" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Grip texture</flux:label>
                                <flux:input wire:model="grip_texture" placeholder="e.g., smooth, checkered" />
                                <flux:error name="grip_texture" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Mechanism & Lock -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Mechanism & Lock</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center gap-2">
                                <flux:switch wire:model="is_folding" />
                                <flux:label>Is folding</flux:label>
                            </div>
                            <flux:field>
                                <flux:label>Opening mechanism</flux:label>
                                <flux:input wire:model="opening_mechanism" placeholder="e.g., manual, assisted" />
                                <flux:error name="opening_mechanism" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Lock type</flux:label>
                                <flux:input wire:model="lock_type" placeholder="e.g., liner lock, frame lock" />
                                <flux:error name="lock_type" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Sheath / Scabbard -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Sheath / Scabbard</flux:accordion.heading>
                    <flux:accordion.content>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center gap-2">

                                <flux:switch wire:model="includes_sheath" />
                                <flux:label>Includes sheath</flux:label>
                            </div>
                            <flux:field>
                                <flux:label>Sheath type</flux:label>
                                <flux:input wire:model="sheath_type" placeholder="e.g., kydex, leather" />
                                <flux:error name="sheath_type" />
                            </flux:field>
                            <flux:field>
                                <flux:label>Carry type</flux:label>
                                <flux:input wire:model="carry_type" placeholder="e.g., belt, molle" />
                                <flux:error name="carry_type" />
                            </flux:field>
                        </div>
                    </flux:accordion.content>
                </flux:accordion.item>

                <!-- Package & Notes -->
                <flux:accordion.item>
                    <flux:accordion.heading class="text-blue-500! mb-3">Package & Notes</flux:accordion.heading>
                    <flux:accordion.content>
                        <flux:field class="md:col-span-3 mb-3">
                            <flux:label>Package includes</flux:label>
                            <flux:textarea wire:model="package_includes" rows="3"
                                placeholder="e.g., sharpening tool, extra sheath, manuals" />
                            <flux:error name="package_includes" />
                        </flux:field>

                        <flux:field class="md:col-span-3">
                            <flux:label>Notes</flux:label>
                            <flux:textarea wire:model="notes" rows="4"
                                placeholder="Additional notes or specifications" />
                            <flux:error name="notes" />
                        </flux:field>
                    </flux:accordion.content>
                </flux:accordion.item>

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
