<template>
    <div>
      <label for="HeadlineAct" class="block text-sm font-medium text-gray-900"> Headliner </label>
    
      <div class="relative mt-1.5">
        <input
          type="text"
          list="HeadlineActArtist"
          id="HeadlineAct"
          class="w-full rounded-lg border-gray-300 pe-10 text-gray-700 sm:text-sm [&::-webkit-calendar-picker-indicator]:opacity-0"
          placeholder="Please select"
        />
    
        <span class="absolute inset-y-0 end-0 flex w-8 items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-gray-500"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
            />
          </svg>
        </span>
      </div>
    
      <datalist name="HeadlineAct" id="HeadlineActArtist">
        <option value="JM">John Mayer</option>
        <option value="SRV">Stevie Ray Vaughn</option>
        <option value="JH">Jimi Hendrix</option>
        <option value="BBK">B.B King</option>
        <option value="AK">Albert King</option>
        <option value="BG">Buddy Guy</option>
        <option value="EC">Eric Clapton</option>
      </datalist>
    </div>
  </template>