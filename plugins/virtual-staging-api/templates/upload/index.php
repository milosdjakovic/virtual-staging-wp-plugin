<?php
// Ensure this file is being included by a parent file
if (!defined('ABSPATH'))
    exit;
?>

<div class="ReactModal__Content ReactModal__Content--after-open z-20 flex h-full max-h-screen w-full justify-center overflow-auto bg-white p-4 pt-12 font-outfit outline-none sm:overflow-hidden md:h-auto md:w-auto md:max-w-[90vw] md:rounded-2xl md:p-6 md:pt-6 md:shadow-sm sm:overflow-visible snipcss-7K6H4"
    tabindex="-1" role="dialog" aria-modal="true">
    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round"
        stroke-linejoin="round" class="absolute right-6 top-4 text-2xl md:hidden" height="1em" width="1em"
        xmlns="http://www.w3.org/2000/svg">
        <path d="M18 6 6 18"></path>
        <path d="m6 6 12 12"></path>
    </svg>
    <div class="hidden style-wTgLQ" id="style-wTgLQ">
        <div>
            <div class="mb-6 flex flex-col items-center justify-center gap-3">
                <p class="text-3xl font-bold text-gray-900">Remove furniture?</p>
                <p class="font-medium">We have detected furniture in your image. Do you want to remove it?</p>
            </div>
            <div class="relative mb-6 flex justify-center"></div>
            <div class="flex flex-col-reverse items-center justify-center gap-4 md:flex-row md:justify-end">
                <button
                    class="flex text-sm font-semibold leading-none relative transition-colors duration-75 items-center justify-center gap-1 md:gap-1.5 rounded-xl border-2 px-10 py-3 text-darkgray border-darkgray hover:bg-darkgray hover:text-white w-full">
                    <span>Keep furniture</span>
                </button>
                <button
                    class="flex text-sm font-semibold leading-none relative transition-colors duration-75 items-center justify-center gap-1 md:gap-1.5 rounded-xl border-2 px-10 py-3 text-white bg-primary border-primary hover:bg-primary/90 hover:border-primary/50 w-full">
                    <span>Remove furniture</span>
                </button>
            </div>
        </div>
    </div>
    <div class="relative z-50 max-h-full w-full flex-col flex style-zZbX4" id="style-zZbX4">
        <div class="flex w-full flex-col gap-4 h-auto md:grid-cols-[4fr_3fr] lg:grid lg:max-h-[600px]">
            <div class="relative flex min-h-[150px] justify-center">
                <div class="flex h-full w-full flex-row justify-center max-w-3xl">
                    <div class="flex w-full flex-col">
                        <div class="flex h-auto flex-col items-center md:aspect-[7/5]">
                            <div id="drop-zone" role="presentation" tabindex="0"
                                class="relative flex h-full w-full shrink-0 cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-xl border border-dashed border-black/15 p-4 transition-all duration-75 hover:bg-black/[0.025] min-h-52 md:min-h-[300px]">
                                <input accept="image/png,.png,.jpg,.jpeg,.gif,.webp,.svg,.bmp,.ico,.tiff" tabindex="-1"
                                    type="file" id="file-input" class="style-JvIrZ hidden">

                                <div class="flex items-center gap-2 flex-col text-center">
                                    <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                        stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-xl"
                                        height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" x2="12" y1="3" y2="15"></line>
                                    </svg>

                                    <span>
                                        <span class="md:font-medium md:text-primary">Upload an image</span>
                                        <span class="hidden md:inline"> or drag and drop</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-4">
                <div class="md:hidden"></div>
                <div class="grid gap-2 sm:grid-cols-3">
                    <button
                        class="flex items-center justify-center gap-2 rounded-xl border border-black/15 p-2 text-center transition-all duration-100 bg-black text-white">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                            class="shrink-0 text-lg" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path
                                d="M22 12c0-1.1-.9-2-2-2V7c0-1.1-.9-2-2-2H6c-1.1 0-2 .9-2 2v3c-1.1 0-2 .9-2 2v5h1.33L4 19h1l.67-2h12.67l.66 2h1l.67-2H22v-5zm-4-2h-5V7h5v3zM6 7h5v3H6V7zm-2 5h16v3H4v-3z">
                            </path>
                        </svg>
                        Virtual Staging
                    </button>
                    <button
                        class="hidden flex items-center justify-center gap-2 rounded-xl border border-black/15 p-2 text-center transition-all duration-100 text-black/90 hover:bg-black/10 break-all">
                        <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 256 256"
                            class="shrink-0 text-lg" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M48,64a8,8,0,0,1,8-8H72V40a8,8,0,0,1,16,0V56h16a8,8,0,0,1,0,16H88V88a8,8,0,0,1-16,0V72H56A8,8,0,0,1,48,64ZM184,192h-8v-8a8,8,0,0,0-16,0v8h-8a8,8,0,0,0,0,16h8v8a8,8,0,0,0,16,0v-8h8a8,8,0,0,0,0-16Zm56-48H224V128a8,8,0,0,0-16,0v16H192a8,8,0,0,0,0,16h16v16a8,8,0,0,0,16,0V160h16a8,8,0,0,0,0-16ZM219.31,80,80,219.31a16,16,0,0,1-22.62,0L36.68,198.63a16,16,0,0,1,0-22.63L176,36.69a16,16,0,0,1,22.63,0l20.68,20.68A16,16,0,0,1,219.31,80Zm-54.63,32L144,91.31l-96,96L68.68,208ZM208,68.69,187.31,48l-32,32L176,100.69Z">
                            </path>
                        </svg>
                        Enhancement
                    </button>
                    <button
                        class="hidden flex items-center justify-center gap-2 rounded-xl border border-black/15 p-2 text-center transition-all duration-100 text-black/90 hover:bg-black/10">
                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                            stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-lg" height="1em"
                            width="1em" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 18a5 5 0 0 0-10 0"></path>
                            <line x1="12" y1="9" x2="12" y2="2"></line>
                            <line x1="4.22" y1="10.22" x2="5.64" y2="11.64"></line>
                            <line x1="1" y1="18" x2="3" y2="18"></line>
                            <line x1="21" y1="18" x2="23" y2="18"></line>
                            <line x1="18.36" y1="11.64" x2="19.78" y2="10.22"></line>
                            <line x1="23" y1="22" x2="1" y2="22"></line>
                            <polyline points="16 5 12 9 8 5"></polyline>
                        </svg>
                        Day to Dusk
                    </button>
                </div>
                <div class="flex flex-col gap-2">
                    <div
                        class="hidden flex flex-col items-stretch overflow-hidden rounded-xl border p-4 transition-all duration-100 border-black/15 bg-white hover:bg-iceblue-100 cursor-pointer hover:border-navy-800">
                        <div class="flex items-center gap-2"><svg stroke="currentColor" fill="none" stroke-width="2"
                                viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                                class="text-xl text-navy-800" height="1em" width="1em"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M8 12h8"></path>
                            </svg>
                            <div class="whitespace-nowrap text-base">Remove existing furniture</div>
                            <div sx="[object Object]" class=""><svg stroke="currentColor" fill="none" stroke-width="2"
                                    viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"
                                    class="text-base opacity-50" height="1em" width="1em"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                    <path d="M12 17h.01"></path>
                                </svg>
                            </div>
                            <input class="ml-auto h-5 w-5 rounded border-gray-300 bg-white text-primary focus:ring-2"
                                type="checkbox">
                        </div>
                    </div>
                    <div id="add-furniture-section"
                        class="flex flex-col items-stretch overflow-hidden rounded-xl border p-4 transition-all duration-100 border-black/15 bg-white hover:bg-iceblue-100 cursor-pointer hover:border-navy-800">
                        <div class="flex items-center gap-2">
                            <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24"
                                stroke-linecap="round" stroke-linejoin="round" class="text-xl text-navy-800"
                                height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M8 12h8"></path>
                                <path d="M12 8v8"></path>
                            </svg>
                            <div class="whitespace-nowrap text-base">Add furniture</div>
                            <input id="add-furniture-checkbox"
                                style="visibility: hidden; position: absolute; opacity: 0;"
                                class="ml-auto h-5 w-5 rounded border-gray-300 bg-white text-primary focus:ring-2"
                                type="checkbox">
                        </div>
                        <div id="furniture-options" class="transition-all duration-300">
                            <div class="mt-4">
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2">
                                    <div
                                        class="MuiFormControl-root MuiFormControl-vertical MuiFormControl-sizeMd css-9rix66">
                                        <label for="room-type" id="room-type-label"
                                            class="MuiFormLabel-root css-rtxwyt">
                                            <div class="flex items-center">
                                                <div class="mr-2 inline-block">
                                                    <svg stroke="currentColor" fill="none" stroke-width="2"
                                                        viewBox="0 0 24 24" stroke-linecap="round"
                                                        stroke-linejoin="round" class="text-lg" height="1em" width="1em"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M3 20v-8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v8"></path>
                                                        <path d="M5 10V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v4"></path>
                                                        <path d="M3 18h18"></path>
                                                    </svg>
                                                </div>
                                                Room type
                                            </div>
                                        </label>
                                        <select id="room-type" name="room-type"
                                            class="MuiSelect-root MuiSelect-variantOutlined MuiSelect-colorNeutral MuiSelect-sizeMd !rounded-xl !border-iceblue-100 !px-3 !py-2 !text-heavyblack !backdrop-blur-md transition-colors duration-500 css-1peitnf">
                                            <?php echo $this->generate_select_options($options['roomTypes']); ?>
                                        </select>
                                    </div>
                                    <div
                                        class="MuiFormControl-root MuiFormControl-vertical MuiFormControl-sizeMd z-50 css-9rix66">
                                        <label for="furniture-style" id="furniture-style-label"
                                            class="MuiFormLabel-root css-rtxwyt">
                                            <div class="flex items-center">
                                                <div class="mr-2 inline-block">
                                                    <svg stroke="currentColor" fill="none" stroke-width="2"
                                                        viewBox="0 0 24 24" stroke-linecap="round"
                                                        stroke-linejoin="round" class="text-lg" height="1em" width="1em"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="13.5" cy="6.5" r=".5"></circle>
                                                        <circle cx="17.5" cy="10.5" r=".5"></circle>
                                                        <circle cx="8.5" cy="7.5" r=".5"></circle>
                                                        <circle cx="6.5" cy="12.5" r=".5"></circle>
                                                        <path
                                                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z">
                                                        </path>
                                                    </svg>
                                                </div>Furniture style
                                            </div>
                                        </label>
                                        <select id="furniture-style" name="furniture-style"
                                            class="MuiSelect-root MuiSelect-variantOutlined MuiSelect-colorNeutral MuiSelect-sizeMd !rounded-xl !border-iceblue-100 !px-3 !py-2 !text-heavyblack !backdrop-blur-md transition-colors duration-500 css-1peitnf">
                                            <?php echo $this->generate_select_options($options['styles']); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button id="process-button"
                    class="flex font-semibold relative transition-colors duration-75 items-center justify-center gap-1 md:gap-1.5 cursor-not-allowed opacity-50 rounded-xl border-2 px-10 py-3 text-white bg-primary border-primary mt-auto h-auto w-full text-base"
                    disabled="">
                    <span>Process photo</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var vsaiApiSettings = {
        root: '<?php echo esc_url_raw(rest_url('vsai/v1/')); ?>',
        nonce: '<?php echo wp_create_nonce('wp_rest'); ?>',
        nextPageUrl: '<?php echo esc_js($next_page_url); ?>'
    };
</script>