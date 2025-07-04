<x-filament::page>
    <x-filament::card>
        <div x-data="{ tab: 'fik' }">
            {{-- Tab Navigation --}}
            <div class="flex flex-wrap gap-2 mb-4 justify-center">
                <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                    :class="tab === 'fik1' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'fik1'">
                    FIK LT1
                </button>
                <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                    :class="tab === 'fik2' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'fik2'">
                    FIK LT2
                </button>
                <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                    :class="tab === 'gkb1' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'gkb1'">
                    GKB LT1
                </button>
                <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                    :class="tab === 'gkb2' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'gkb2'">
                    GKB LT2
                </button>
                <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                    :class="tab === 'gkb3' ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                    @click="tab = 'gkb3'">
                    GKB LT3
                </button>
            </div>

            {{-- FIK LT1 --}}
            <div x-show="tab === 'fik1'">
                <x-filament::card>
                    <div x-data="{ tab: 'bandwidth' }">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-4">Gedung Fakultas Ilmu Komputer Lantai 1</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Pilih tab di bawah untuk melihat laporan yang berbeda.
                            </p>
                        </div>

                        {{-- Button --}}
                        <div class="flex flex-wrap gap-2 mb-4 justify-center">
                            {{-- CPU Trend FIK LT1 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'cpufiklt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'cpufiklt1'">
                                CPU Trend
                            </button>
                            {{-- Memory Trend FIK LT1 --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'memoryfiklt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'memoryfiklt1'">
                                Memory Trend
                            </button>
                            {{-- DHCP Trend FIK LT1 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'dhcpfiklt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'dhcpfiklt1'">
                                DHCP Trend
                            </button>
                            {{-- ICMP Ping FIK LT1 Report --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'icmpfiklt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'icmpfiklt1'">
                                ICMP Ping Trend
                            </button> --}}
                            {{-- Link Status Trend FIK LT1 --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'linkfiklt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'linkfiklt1'">
                                Link Status Trend
                            </button> --}}
                            {{-- Traffic Trend FIK LT1 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'trafficfiklt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'trafficfiklt1'">
                                Traffic Trend
                            </button>
                        </div>

                        <hr>
                        <br>

                        {{-- CPU Trend FIK LT1 Report --}}
                        <div x-show="tab === 'cpufiklt1'">
                            <livewire:cpu-trend-report hostName="mikrotik-fik-2" />
                        </div>
                        {{-- Memory Trend FIK LT1 --}}
                        <div x-show="tab === 'memoryfiklt1'">
                            <livewire:memory-trend-report hostName="mikrotik-fik-2" />
                        </div>
                        {{-- DHCP Trend FIK LT1 Report --}}
                        <div x-show="tab === 'dhcpfiklt1'">
                            <livewire:user-trend-report hostName="mikrotik-fik-2" />
                        </div>
                        {{-- ICMP Ping FIK LT1 Report --}}
                        {{-- <div x-show="tab === 'icmpfiklt1'">
                            <livewire:icmp-ping-trend-report hostName="mikrotik-fik-2" />
                        </div> --}}
                        {{-- Link Status Trend FIK LT1 --}}
                        {{-- <div x-show="tab === 'linkfiklt1'">
                            <livewire:link-status-trend-report hostName="mikrotik-fik-2" />
                        </div> --}}
                        {{-- Traffic Trend FIK LT1 Report --}}
                        <div x-show="tab === 'trafficfiklt1'">
                            <livewire:traffic-trend-report hostName="mikrotik-fik-2"
                                interfaceIn="net.if.in[ifHCInOctets.2]" interfaceOut="net.if.out[ifHCOutOctets.2]" />
                        </div>

                    </div>
                </x-filament::card>
            </div>

            {{-- FIK LT2 --}}
            <div x-show="tab === 'fik2'">
                {{-- CPU Trend FIK LT2 Report --}}
                <x-filament::card>
                    <div x-data="{ tab: 'bandwidth' }">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-4">Gedung Fakultas Ilmu Komputer Lantai 2</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Pilih tab di bawah untuk melihat laporan yang berbeda.
                            </p>
                        </div>

                        {{-- Button --}}
                        {{-- CPU Trend FIK LT1 Report --}}
                        <div class="flex flex-wrap gap-2 mb-4 justify-center">
                            {{-- CPU Trend FIK LT1 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'cpufiklt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'cpufiklt2'">
                                CPU Trend
                            </button>
                            {{-- Memory Trend FIK LT2 --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'memoryfiklt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'memoryfiklt2'">
                                Memory Trend
                            </button>
                            {{-- DHCP Trend FIK LT2 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'dhcpfiklt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'dhcpfiklt2'">
                                DHCP Count Trend
                            </button>
                            {{-- ICMP Ping FIK LT2 Report --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'icmpfiklt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'icmpfiklt2'">
                                ICMP Ping Trend
                            </button> --}}
                            {{-- Link Status Trend FIK LT2 --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'linkfiklt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'linkfiklt2'">
                                Link Status Trend
                            </button> --}}
                            {{-- Traffic Trend FIK LT2 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'trafficfiklt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'trafficfiklt2'">
                                Traffic Trend
                            </button>
                        </div>


                        <hr>
                        <br>

                        {{-- CPU Trend FIK LT1 Report --}}
                        <div x-show="tab === 'cpufiklt2'">
                            <livewire:cpu-trend-report hostName="mikrotik-fik-msi" />
                        </div>
                        {{-- Memory Trend FIK LT2 --}}
                        <div x-show="tab === 'memoryfiklt2'">
                            <livewire:memory-trend-report hostName="mikrotik-fik-msi" />
                        </div>
                        {{-- DHCP Trend FIK LT2 Report --}}
                        <div x-show="tab === 'dhcpfiklt2'">
                            <livewire:user-trend-report hostName="mikrotik-fik-msi"
                                interfaceIn="net.if.in[ifHCInOctets.2]" interfaceOut="net.if.out[ifHCOutOctets.2]" />
                        </div>
                        {{-- ICMP Ping FIK LT2 Report --}}
                        {{-- <div x-show="tab === 'icmpfiklt2'">
                            <livewire:icmp-ping-trend-report hostName="mikrotik-fik-msi" />
                        </div> --}}
                        {{-- Link Status Trend FIK LT2 --}}
                        {{-- <div x-show="tab === 'linkfiklt2'">
                            <livewire:link-status-trend-report hostName="mikrotik-fik-msi" />
                        </div> --}}
                        {{-- Traffic Trend FIK LT2 Report --}}
                        <div x-show="tab === 'trafficfiklt2'">
                            <livewire:traffic-trend-report hostName="mikrotik-fik-msi"
                                interfaceIn="net.if.in[ifHCInOctets.2]" interfaceOut="net.if.out[ifHCOutOctets.2]" />
                        </div>
                    </div>
                </x-filament::card>
            </div>


            {{-- GKB LT1 --}}
            <div x-show="tab === 'gkb1'">
                <x-filament::card>
                    <div x-data="{ tab: 'bandwidth' }">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-4">Gedung Kuliah Bersama Lantai 1</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Pilih tab di bawah untuk melihat laporan yang berbeda.
                            </p>
                        </div>

                        {{-- Button --}}
                        <div class="flex flex-wrap gap-2 mb-4 justify-center">
                            {{-- CPU Trend GKB LT1 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'cpugkblt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'cpugkblt1'">
                                CPU Trend
                            </button>
                            {{-- Memory Trend GKB LT1 --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'memoryfiklt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'memoryfiklt1'">
                                Memory Trend
                            </button>
                            {{-- DHCP Lease Trend GKB LT1 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'dhcpgkblt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'dhcpgkblt1'">
                                DHCP Count Trend
                            </button>
                            {{-- ICMP Ping GKB LT1 Report --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'icmpgkblt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'icmpgkblt1'">
                                ICMP Ping Trend
                            </button> --}}
                            {{-- Link Status Trend GKB LT1 --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'linkgkblt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'linkgkblt1'">
                                Link Status Trend
                            </button> --}}
                            {{-- Traffic Trend GKB LT1 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'trafficgkblt1' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'trafficgkblt1'">
                                Traffic Trend
                            </button>
                        </div>

                        <hr>
                        <br>

                        {{-- Tab --}}
                        {{-- CPU Trend GKB LT1 Report --}}
                        <div x-show="tab === 'cpugkblt1'">
                            <livewire:cpu-trend-report hostName="mikrotik-gkb-lt1" />
                        </div>
                        {{-- Memory Trend GKB LT1 --}}
                        <div x-show="tab === 'memoryfiklt1'">
                            <livewire:memory-trend-report hostName="mikrotik-gkb-lt1" />
                        </div>
                        {{-- DHCP Lease Trend GKB LT1 Report --}}
                        <div x-show="tab === 'dhcpgkblt1'">
                            <livewire:user-trend-report hostName="mikrotik-gkb-lt1" />
                        </div>
                        {{-- ICMP Ping GKB LT1 Report --}}
                        {{-- <div x-show="tab === 'icmpgkblt1'">
                            <livewire:icmp-ping-trend-report hostName="mikrotik-gkb-lt1" />
                        </div> --}}
                        {{-- Link Status Trend GKB LT1 --}}
                        {{-- <div x-show="tab === 'linkgkblt1'">
                            <livewire:link-status-trend-report hostName="mikrotik-gkb-lt1" />
                        </div> --}}
                        {{-- Traffic Trend GKB LT1 Report --}}
                        <div x-show="tab === 'trafficgkblt1'">
                            <livewire:traffic-trend-report hostName="mikrotik-gkb-lt1"
                                interfaceIn="net.if.in[ifHCInOctets.2]" interfaceOut="net.if.out[ifHCOutOctets.2]" />
                        </div>
                    </div>
                </x-filament::card>
            </div>

            {{-- GKB LT2 --}}
            <div x-show="tab === 'gkb2'">
                <x-filament::card>
                    <div x-data="{ tab: 'bandwidth' }">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-4">Gedung Kuliah Bersama Lantai 2</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Pilih tab di bawah untuk melihat laporan yang berbeda.
                            </p>
                        </div>

                        {{-- Button --}}
                        <div class="flex flex-wrap gap-2 mb-4 justify-center">
                            {{-- CPU Trend GKB LT2 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'cpugkblt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'cpugkblt2'">
                                CPU Trend
                            </button>
                            {{-- Memory Trend GKB LT2 --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'memorygkblt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'memorygkblt2'">
                                Memory Trend
                            </button>
                            {{-- ICMP Ping GKB LT2 Report --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'icmpgkblt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'icmpgkblt2'">
                                ICMP Ping Trend
                            </button> --}}
                            {{-- Link Status Trend GKB LT2 --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'linkgkblt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'linkgkblt2'">
                                Link Status Trend
                            </button> --}}
                            {{-- Traffic Trend GKB LT2 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'trafficgkblt2' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'trafficgkblt2'">
                                Traffic Trend
                            </button>
                        </div>

                        <hr>
                        <br>

                        {{-- Tab --}}
                        {{-- CPU Trend GKB LT2 Report --}}
                        <div x-show="tab === 'cpugkblt2'">
                            <livewire:cpu-trend-report hostName="mikrotik-gkb-lt2" />
                        </div>
                        {{-- Memory Trend GKB LT2 --}}
                        <div x-show="tab === 'memorygkblt2'">
                            <livewire:memory-trend-report hostName="mikrotik-gkb-lt2" />
                        </div>
                        {{-- ICMP Ping GKB LT2 Report --}}
                        {{-- <div x-show="tab === 'icmpgkblt2'">
                            <livewire:icmp-ping-trend-report hostName="mikrotik-gkb-lt2" />
                        </div> --}}
                        {{-- Link Status Trend GKB LT2 --}}
                        {{-- <div x-show="tab === 'linkgkblt2'">
                            <livewire:link-status-trend-report hostName="mikrotik-gkb-lt2" />
                        </div> --}}
                        {{-- Traffic Trend GKB LT2 Report --}}
                        <div x-show="tab === 'trafficgkblt2'">
                            <livewire:traffic-trend-report hostName="mikrotik-gkb-lt2"
                                interfaceIn="net.if.in[ifHCInOctets.1]" interfaceOut="net.if.out[ifHCOutOctets.1]" />
                        </div>
                    </div>
                </x-filament::card>
            </div>

            {{-- GKB LT3 --}}
            <div x-show="tab === 'gkb3'">
                <x-filament::card>
                    <div x-data="{ tab: 'bandwidth' }">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold mb-4">Gedung Kuliah Bersama Lantai 3</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Pilih tab di bawah untuk melihat laporan yang berbeda.
                            </p>
                        </div>

                        {{-- Button --}}
                        <div class="flex flex-wrap gap-2 mb-4 justify-center">
                            {{-- CPU Trend GKB LT3 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'cpugkblt3' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'cpugkblt3'">
                                CPU Trend
                            </button>
                            {{-- Memory Trend GKB LT3 --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'memorygkblt3' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'memorygkblt3'">
                                Memory Trend
                            </button>
                            {{-- ICMP Ping GKB LT3 Report --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'icmpgkblt3' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'icmpgkblt3'">
                                ICMP Ping Trend
                            </button> --}}
                            {{-- Link Status Trend GKB LT3 --}}
                            {{-- <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'linkgkblt3' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'linkgkblt3'">
                                Link Status Trend
                            </button> --}}
                            {{-- Traffic Trend GKB LT3 Report --}}
                            <button class="px-4 py-2 rounded-lg font-semibold space-x-4"
                                :class="tab === 'trafficgkblt3' ? 'bg-primary-600 text-white' :
                                    'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                                @click="tab = 'trafficgkblt3'">
                                Traffic Trend
                            </button>
                        </div>

                        <hr>
                        <br>

                        {{-- Tab --}}
                        {{-- CPU Trend GKB LT3 Report --}}
                        <div x-show="tab === 'cpugkblt3'">
                            <livewire:cpu-trend-report hostName="mikrotik-gkb-lt3" />
                        </div>
                        {{-- Memory Trend GKB LT3 --}}
                        <div x-show="tab === 'memorygkblt3'">
                            <livewire:memory-trend-report hostName="mikrotik-gkb-lt3" />
                        </div>
                        {{-- ICMP Ping GKB LT3 Report --}}
                        {{-- <div x-show="tab === 'icmpgkblt3'">
                <livewire:icmp-ping-trend-report hostName="mikrotik-gkb-lt3" />
            </div> --}}
                        {{-- Link Status Trend GKB LT3 --}}
                        {{-- <div x-show="tab === 'linkgkblt3'">
                <livewire:link-status-trend-report hostName="mikrotik-gkb-lt3" />
            </div> --}}
                        {{-- Traffic Trend GKB LT3 Report --}}
                        <div x-show="tab === 'trafficgkblt3'">
                            <livewire:traffic-trend-report hostName="mikrotik-gkb-lt3"
                                interfaceIn="net.if.in[ifHCInOctets.1]" interfaceOut="net.if.out[ifHCOutOctets.1]" />
                        </div>
                    </div>
                </x-filament::card>
            </div>


        </div>
    </x-filament::card>
</x-filament::page>
