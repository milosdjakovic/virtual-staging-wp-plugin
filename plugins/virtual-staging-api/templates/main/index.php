<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH'))
    exit;
?>

<main style="flex-direction: column;" class="w-full flex  container my-24 snipcss-YTm7a">
    <div class="flex w-full" style="align-items: flex-start;">
        <button id="uploadAnotherImageButton"
            class="flex items-center justify-center text-sm self-start font-semibold leading-none transition-colors duration-75 gap-1 md:gap-1.5 rounded-xl border-2 px-10 py-3 text-darkgray border-darkgray hover:bg-darkgray hover:text-white">
            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
                stroke-linejoin="round" class="text-base" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 12H5"></path>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Upload Another Image</span>
            <span id="tokenStatusDisplay" class="ml-2 text-xs"></span>
        </button>
    </div>

    <div class="flex flex-col-reverse gap-4 md:flex-row mt-4">
        <div class="md:w-1/3" id="renderPageOriginalContainer">
            <h3 class="font-semibold mb-2 text-gray-800 text-lg md:text-xl mt-0">Original</h3>
            <div class="">
                <div class="group w-full overflow-hidden rounded-xl cursor-pointer relative style-1oN9o"
                    id="style-1oN9o">
                    <img class="h-full w-full bg-gray-100 object-contain transition-opacity group-hover:opacity-70"
                        src="" alt="" loading="lazy"><svg stroke="currentColor" fill="none" stroke-width="2"
                        viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                        class="absolute left-1/2 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 opacity-0 transition-all group-hover:opacity-80"
                        height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" x2="12" y1="15" y2="3"></line>
                    </svg>
                </div>
            </div>

            <!-- Room Type Selection -->
            <div class="mt-4 style-o48r9" id="style-o48r9">
                <div class="MuiFormControl-root MuiFormControl-vertical MuiFormControl-sizeMd css-9rix66">
                    <label for=":r16u:" id=":r16u:-label" class="MuiFormLabel-root css-rtxwyt">
                        <div class="flex items-center">
                            <div class="mr-2 inline-block"><svg stroke="currentColor" fill="none" stroke-width="2"
                                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" class="text-lg"
                                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 20v-8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"></path>
                                    <path d="M5 10V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v4"></path>
                                    <path d="M3 18h18"></path>
                                </svg></div>Room type
                        </div>
                    </label>
                    <div
                        class="MuiSelect-root MuiSelect-variantOutlined MuiSelect-colorNeutral MuiSelect-sizeMd !rounded-xl !border-iceblue-100 !px-3 !py-2 !text-heavyblack !backdrop-blur-md transition-colors duration-500 css-1peitnf">
                        <select role="combobox" aria-labelledby=":r16u:-label" id=":r16u:" name="radio-buttons-group"
                            class="MuiSelect-button css-1qmzz5g room-type-select w-full">
                            <?php echo $this->generate_select_options($options['roomTypes']); ?>
                        </select>
                    </div>
                </div>

                <!-- Furniture Style Selection -->
                <div class="MuiFormControl-root MuiFormControl-vertical MuiFormControl-sizeMd mt-2 w-full css-9rix66">
                    <label for=":r170:" id=":r170:-label" class="MuiFormLabel-root css-rtxwyt">
                        <div class="flex items-center">
                            <div class="mr-2 inline-block"><svg stroke="currentColor" fill="none" stroke-width="2"
                                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" class="text-lg"
                                    height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="13.5" cy="6.5" r=".5"></circle>
                                    <circle cx="17.5" cy="10.5" r=".5"></circle>
                                    <circle cx="8.5" cy="7.5" r=".5"></circle>
                                    <circle cx="6.5" cy="12.5" r=".5"></circle>
                                    <path
                                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z">
                                    </path>
                                </svg></div>Furniture style
                        </div>
                    </label>
                    <div
                        class="MuiSelect-root MuiSelect-variantOutlined MuiSelect-colorNeutral MuiSelect-sizeMd !rounded-xl !border-iceblue-100 !px-3 !py-2 !text-heavyblack !backdrop-blur-md transition-colors duration-500 css-1peitnf">
                        <select role="combobox" aria-labelledby=":r170:-label" id=":r170:" name="radio-buttons-group"
                            class="MuiSelect-button css-1qmzz5g furniture-style-select w-full">
                            <?php echo $this->generate_select_options($options['styles']); ?>
                        </select>
                    </div>
                </div>

                <!-- Generate Variation Button -->
                <div class="mb-4 mt-2 md:mb-0">
                    <button id="generateVariationButton"
                        class="flex text-sm font-semibold leading-none relative transition-colors duration-75 items-center justify-center gap-1 md:gap-1.5 rounded-xl border-2 px-10 py-3 text-darkgray border-darkgray hover:bg-darkgray hover:text-white mb-2 w-full shrink-0 md:mt-2 md:w-full"
                        disabled="true">
                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                            stroke-linecap="round" stroke-linejoin="round" class="text-base" height="1em" width="1em"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                            <path d="M3 3v5h5"></path>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path>
                            <path d="M16 16h5v5"></path>
                        </svg>
                        <span>Generate Variation</span>
                    </button>

                    <span class="flex items-center justify-center text-center text-sm text-gray-400">
                        Unlimited variations and downloads included
                    </span>
                </div>
            </div>
        </div>
        <div class="md:w-2/3" id="renderPageResultsContainer">
            <div class="mb-2 flex items-center space-x-4">
                <h3 class="font-semibold text-gray-800 text-lg md:text-xl mt-0">Results</h3>

            </div>
            <div class="relative h-full">
                <div id="loading-indicator" class="absolute bg-white flex h-full inset-0 items-center justify-center"
                    style="background: rgba(249, 249, 249, 0.8); z-index: 31;">
                    <p class="text-lg">Loading...</p>
                </div>

                <div class="overflow-hidden h-full">
                    <div style="flex-direction: column;"
                        class="carousel-root render_carousel left-right-dim group rounded-lg flex h-full">

                        <div class="carousel carousel-slider " id="main-carousel" style="flex: 1;">

                            <div style="min-height: 450px" class="slider-wrapper axis-horizontal h-full">
                                <div id="download-image-overlay" style="background: rgba(0, 0, 0, 0.8);"
                                    class="absolute h-full hidden test w-full z-20 flex rounded-xl justify-center items-center opacity-0 hover:opacity-100 cursor-pointer transition-opacity duration-75">
                                    <div class="flex justify-center items-center gap-1 text-white">
                                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-base"
                                            height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="7 10 12 15 17 10"></polyline>
                                            <line x1="12" y1="15" x2="12" y2="3"></line>
                                        </svg>

                                        <p class="text-white font-semibold text-lg">Download Image</p>
                                    </div>
                                </div>

                                <div id="main-carousel-image" class="absolute inset-0">
                                    <!-- Main slides will be dynamically inserted here -->
                                </div>
                            </div>


                        </div>
                        <div class="carousel ">
                            <div style="overflow-x: auto;" class="thumbs-wrapper axis-vertical flex">
                                <div id="generating-variation-indicator"
                                    style="background: rgba(0, 0, 0, 0.8); padding: 6px 12px;"
                                    class="rounded-xl flex justify-center items-center mr-2 hidden">
                                    <p class="text-white">Generating variation...</p>
                                </div>

                                <ul class="thumbs animated" id="thumbnail-slider">
                                    <!-- Thumbnails will be dynamically inserted here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript">
    var vsaiApiSettings = {
        root: '<?php echo esc_url_raw(rest_url('vsai/v1/')); ?>',
        nonce: '<?php echo wp_create_nonce('wp_rest'); ?>',
        nextPageUrl: '<?php echo esc_js($next_page_url); ?>'
    };
</script>