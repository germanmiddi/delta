
<template>
  <div class="min-h-full">

    <main class="py-10">
      <!-- Page header -->
      <div class="max-w-3xl mx-auto px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
        <div class="flex items-center space-x-5">
            <h1 class="text-2xl font-bold text-gray-900">Proximos pedidos</h1>
        </div>
        <!-- <div class="mt-6 flex flex-col-reverse justify-stretch space-y-4 space-y-reverse sm:flex-row-reverse sm:justify-end sm:space-x-reverse sm:space-y-0 sm:space-x-3 md:mt-0 md:flex-row md:space-x-3">
          <button type="button" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">Disqualify</button>
          <button type="button" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-blue-500">Advance to offer</button>
        </div> -->
      </div>

      <div class="mt-8 max-w-3xl mx-auto grid grid-cols-1 gap-6 sm:px-6 lg:max-w-7xl lg:grid-flow-col-dense lg:grid-cols-3">
        <div class="space-y-6 lg:col-start-1 lg:col-span-2">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
            <li v-for="position in positions" :key="position.id">
                <a href="#" class="block hover:bg-gray-50">
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-indigo-600 truncate">
                        {{ position.title }}
                    </p>
                    <div class="ml-2 flex-shrink-0 flex">
                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        {{ position.type }}
                        </p>
                    </div>
                    </div>
                    <div class="mt-2 sm:flex sm:justify-between">
                    <div class="sm:flex">
                        <p class="flex items-center text-sm text-gray-500">
                        <UsersIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                        {{ position.department }}
                        </p>
                        <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                        <LocationMarkerIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                        {{ position.location }}
                        </p>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                        <CalendarIcon class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                        <p>
                        Closing on
                        {{ ' ' }}
                        <time :datetime="position.closeDate">{{ position.closeDateFull }}</time>
                        </p>
                    </div>
                    </div>
                </div>
                </a>
            </li>
            </ul>
        </div>          
        </div>

        <section aria-labelledby="timeline-title" class="lg:col-start-3 lg:col-span-1">
          <div class="bg-white px-4 py-5 shadow sm:rounded-lg sm:px-6">
            <div class="flex items-center justify-between">
                <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                <div> 12 Julio 2022</div>
                <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
            </div>
            
            <div class="mt-6 flex flex-col justify-stretch">
              <button type="button" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Nuevo Pedido</button>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script>
import {
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  Popover,
  PopoverButton,
  PopoverOverlay,
  PopoverPanel,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue'
import {
  ArrowNarrowLeftIcon,
  CheckIcon,
  HomeIcon,
  PaperClipIcon,
  QuestionMarkCircleIcon,
  SearchIcon,
  ThumbUpIcon,
  UserIcon,
  ChevronLeftIcon,
  ChevronRightIcon,  
} from '@heroicons/vue/solid'
import { BellIcon, MenuIcon, XIcon } from '@heroicons/vue/outline'
import { CalendarIcon, LocationMarkerIcon, UsersIcon } from '@heroicons/vue/solid'


const user = {
  name: 'Whitney Francis',
  email: 'whitney@example.com',
  imageUrl:
    'https://images.unsplash.com/photo-1517365830460-955ce3ccd263?ixlib=rb-=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=256&h=256&q=80',
}
const navigation = [
  { name: 'Dashboard', href: '#' },
  { name: 'Jobs', href: '#' },
  { name: 'Applicants', href: '#' },
  { name: 'Company', href: '#' },
]
const breadcrumbs = [
  { name: 'Jobs', href: '#', current: false },
  { name: 'Front End Developer', href: '#', current: false },
  { name: 'Applicants', href: '#', current: true },
]
const userNavigation = [
  { name: 'Your Profile', href: '#' },
  { name: 'Settings', href: '#' },
  { name: 'Sign out', href: '#' },
]
const attachments = [
  { name: 'resume_front_end_developer.pdf', href: '#' },
  { name: 'coverletter_front_end_developer.pdf', href: '#' },
]
const eventTypes = {
  applied: { icon: UserIcon, bgColorClass: 'bg-gray-400' },
  advanced: { icon: ThumbUpIcon, bgColorClass: 'bg-blue-500' },
  completed: { icon: CheckIcon, bgColorClass: 'bg-green-500' },
}
const timeline = [
  {
    id: 1,
    type: eventTypes.applied,
    content: 'Applied to',
    target: 'Front End Developer',
    date: 'Sep 20',
    datetime: '2020-09-20',
  },
  {
    id: 2,
    type: eventTypes.advanced,
    content: 'Advanced to phone screening by',
    target: 'Bethany Blake',
    date: 'Sep 22',
    datetime: '2020-09-22',
  },
  {
    id: 3,
    type: eventTypes.completed,
    content: 'Completed phone screening with',
    target: 'Martha Gardner',
    date: 'Sep 28',
    datetime: '2020-09-28',
  },
  {
    id: 4,
    type: eventTypes.advanced,
    content: 'Advanced to interview by',
    target: 'Bethany Blake',
    date: 'Sep 30',
    datetime: '2020-09-30',
  },
  {
    id: 5,
    type: eventTypes.completed,
    content: 'Completed interview with',
    target: 'Katherine Snyder',
    date: 'Oct 4',
    datetime: '2020-10-04',
  },
]
const comments = [
  {
    id: 1,
    name: 'Leslie Alexander',
    date: '4d ago',
    imageId: '1494790108377-be9c29b29330',
    body: 'Ducimus quas delectus ad maxime totam doloribus reiciendis ex. Tempore dolorem maiores. Similique voluptatibus tempore non ut.',
  },
  {
    id: 2,
    name: 'Michael Foster',
    date: '4d ago',
    imageId: '1519244703995-f4e0f30006d5',
    body: 'Et ut autem. Voluptatem eum dolores sint necessitatibus quos. Quis eum qui dolorem accusantium voluptas voluptatem ipsum. Quo facere iusto quia accusamus veniam id explicabo et aut.',
  },
  {
    id: 3,
    name: 'Dries Vincent',
    date: '4d ago',
    imageId: '1506794778202-cad84cf45f1d',
    body: 'Expedita consequatur sit ea voluptas quo ipsam recusandae. Ab sint et voluptatem repudiandae voluptatem et eveniet. Nihil quas consequatur autem. Perferendis rerum et.',
  },
]

const positions = [
  {
    id: 1,
    title: 'Back End Developer',
    type: 'Full-time',
    location: 'Remote',
    department: 'Engineering',
    closeDate: '2020-01-07',
    closeDateFull: 'January 7, 2020',
  },
  {
    id: 2,
    title: 'Front End Developer',
    type: 'Full-time',
    location: 'Remote',
    department: 'Engineering',
    closeDate: '2020-01-07',
    closeDateFull: 'January 7, 2020',
  },
  {
    id: 3,
    title: 'User Interface Designer',
    type: 'Full-time',
    location: 'Remote',
    department: 'Design',
    closeDate: '2020-01-14',
    closeDateFull: 'January 14, 2020',
  },
]

export default {
  components: {
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    Popover,
    PopoverButton,
    PopoverOverlay,
    PopoverPanel,
    TransitionChild,
    TransitionRoot,
    ArrowNarrowLeftIcon,
    BellIcon,
    HomeIcon,
    MenuIcon,
    PaperClipIcon,
    QuestionMarkCircleIcon,
    SearchIcon,
    XIcon,
    CalendarIcon,
    LocationMarkerIcon,
    UsersIcon,    
        ChevronLeftIcon,
    ChevronRightIcon,
  },
  setup() {
    return {
      user,
      navigation,
      breadcrumbs,
      userNavigation,
      attachments,
      eventTypes,
      timeline,
      comments,
      positions,
    }
  },
}
</script>