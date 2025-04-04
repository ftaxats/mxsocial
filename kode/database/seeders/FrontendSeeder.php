<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use App\Models\Admin\Frontend;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
class FrontendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $frontendsKeys = Frontend::distinct()->pluck('key')->toArray();

        $sections =  [

            "contact_us" => [

                "content"  => [
                    "support_title" => "Are you an existing customer?",
                    "support_description" => "If so, please click the button on the right to open a support ticket.",
                    "button_name" => "Open Support Ticket",
                    "button_url" => "@@@",
                    "section_title" => "Contact us",
                    "section_heading" => "Growth is the only constant in our",
                    "section_description" => "We welcome all the questions & remarks. Growth is the only constant in our",
                    "breadcrumb_title" => "Get In Touch With us",
                    "opening_hour_text" => "08:00 - 17:00",
                    "breadcrumb_description" => "Our 24/7 support experts are here to assist you through tough times, so you get back to building exciting projects",
                ],

                

            ],

            "feedback" => [

                "content"  => [
                    "breadcrumb_title" => "Get In Touch With us",
                    "breadcrumb_description" => "Our 24/7 support experts are here to assist you through tough times, so you get back to building exciting projects",
                    "heading" => "We'd love hear from you",
                    "description" => "We welcome all the questions & remarks. Growth is the only constant in our",
                ],

                

            ],

            "cta" => [

                "content"  => [
                    "title" => "Ready to get those mind-blowing ideas?",
                    "description" => "Track the engagement rate, comments, likes, shares, and impressions for each post, so you know whats working best for your audience. Once youve identified your high-performing posts, you can share them again."
                ],

                "element" => [
                    [
                        "button_name" => "Get Started",
                        "url" =>  "@@",
                    ],
                    [
                        "button_name" => "Contact Us",
                        "url" =>  "@@",
                    ]
                ]

            ],

            "cookie" => [

                "content"  => [
                    "description" => "We use cookies to enhance your browsing experience. By clicking 'Accept all, you agree to the use of cookies."
                ],

            ],

            "banner" => [
                "content"  => [
                    "title"             => "Social <span>Media</span> 10x Faster <br> With AI <span>",
                    "description"       => "Our all-in-one social media management platform unlocks the full potential of social to transform not just your marketing strategy—but every area of your organization.",
                    "button_name"       =>  "Discover more",
                    "button_icon"       =>  "bi bi-arrow-up-right-circle",
                    "button_URL"        =>  "@@",
                    "video_URL"         =>  "@@"
                ],  
                "element" => [
                    [
                    ],
                    [
                    ],
                    [
                    ],
                    [
                    ],
                    [
                    ]
                ]

            ],

            "about" => [

                "content" => [
                    "title"        => "Our Values",
                    "sub_title"    => "About us",
                    "description"  => "Discover the power of our secure an rewarding credit cards. Discover th power of our secure and rewarding credit cards. Discover the power of our secure an rewarding credit cards. Discover th power of our secure and rewarding credit cards.",
                ],

                "element" => [

                    [
                        "icon"  => "bi bi-heart",
                        "title"  => "Takeover",
                        "description"  => "Discover the power of our secure an rewarding credit cards. Discover th power of our secure and rewarding credit cards."
                    ],
                    [
                        "icon"  => "bi bi-heart",
                        "title"  => "Takeover",
                        "description"  => "Discover the power of our secure an rewarding credit cards. Discover th power of our secure and rewarding credit cards."
                    ],
                    [
                        "icon"  => "bi bi-heart",
                        "title"  => "Takeover",
                         "description"  => "Discover the power of our secure an rewarding credit cards. Discover th power of our secure and rewarding credit cards."
                    ],
                    [
                        "icon"  => "bi bi-heart",
                        "title"  => "Trustworthy",
                         "description"  => "Discover the power of our secure an rewarding credit cards. Discover th power of our secure and rewarding credit cards."
                    ]

                ]
                
            ],

            "about_counter" => [

                "element" => [
                    [
                        "counter_value"  => "01",
                        "counter_text"  => "300+Our Customers",
      
                    ],
                    [
                        "counter_value"  => "01",
                        "counter_text"  => "300+Our Customers",
                    ]
                ]
                
            ],

     

            "integration" => [

                "content"  => [
                    "title" =>  "Our Intregration",
                ],

                "element" => [
                    [
                        "title"  => "Linkedin",
                        "short_description"  => "Excited to introduce our latest innovation! Discover the future of Linkedin",
                    ],
                    [
                        "title"  => "Twitter",
                        "short_description"  => "Excited to introduce our latest innovation! Discover the future of Linkedin",
                    ],
                    [
                        "title"  => "Instragram",
                        "short_description"  => "Excited to introduce our latest innovation! Discover the future of Linkedin",
                    ],
                    [
                        "title"  => "Facebook",
                        "short_description"  => "Excited to introduce our latest innovation! Discover the future of Linkedin",
                    ]
               
                ]

            ],


            

            "feature" => [

                "content"  => [

                    "title" =>  "Transforming Social With <span>Wealth Management</span>",
                    "sub_title" =>  "Key Features",

                ],

                "element" => [

                    [
                        "title"       =>  "Social Media Calendar",
                        "description" =>  "Discover the power of our secure and rewarding credit cards. Discover the power of our secure and rewarding credit cards.",
                    ],
                    [
                        "title"       =>  "Bulk Scheduling",
                        "description" =>  "Discover the power of our secure and rewarding credit cards. Discover the power of our secure and rewarding credit cards.",
                    ],
                    [
                        "title"       =>  "AI Assistant",
                        "description" =>  "Discover the power of our secure and rewarding credit cards. Discover the power of our secure and rewarding credit cards.",
                    ],
                    [
                        "title"       =>  "Engagement",
                        "description" =>  "Discover the power of our secure and rewarding credit cards. Discover the power of our secure and rewarding credit cards.",
                    ]

                ]


            ],


            "powerful_feature" => [

                "content"  => [

                    "title" =>  "The best features for you",
                    "sub_title" =>  "Powerful features",
                    "description" =>  "Unlock the power of Social posting Enhance your experience and enjoy seamless [benefit]. Try it now and see the difference.",

                ],


                
                "element" => [

                    [
                        "title"       =>  "Optimization Engine Rank",
                        "description" =>  "Discover our powerful features designed to elevate your experience. From cutting-edge technology to user-friendly interfaces, our features are crafted to provide maximum efficiency and unparalleled performance. Experience the difference today.",
                    ],
                 
                    [
                        "title"       =>  "Optimization Engine Rank",
                        "description" =>  "Discover our powerful features designed to elevate your experience. From cutting-edge technology to user-friendly interfaces, our features are crafted to provide maximum efficiency and unparalleled performance. Experience the difference today.",
                    ],
                 
                    [
                        "title"       =>  "Optimization Engine Rank",
                        "description" =>  "Discover our powerful features designed to elevate your experience. From cutting-edge technology to user-friendly interfaces, our features are crafted to provide maximum efficiency and unparalleled performance. Experience the difference today.",
                    ],
                 
                    [
                        "title"       =>  "Optimization Engine Rank",
                        "description" =>  "Discover our powerful features designed to elevate your experience. From cutting-edge technology to user-friendly interfaces, our features are crafted to provide maximum efficiency and unparalleled performance. Experience the difference today.",
                    ],
                 
                    [
                        "title"       =>  "Optimization Engine Rank",
                        "description" =>  "Discover our powerful features designed to elevate your experience. From cutting-edge technology to user-friendly interfaces, our features are crafted to provide maximum efficiency and unparalleled performance. Experience the difference today.",
                    ],
                 

                ]
                

            ],



            "service" => [

                "content"  => [

                    "title" =>  "Empowering social media <span>insights</span>",
                    "sub_title" =>  "Service",
                    "description" =>  "Discover the power of our secure and rewarding credit cards.",
                    "section_top_title" =>  "Unlock the power of social media <span>insights</span> to drive your strategy forward. ",
                    "section_bottom_title" =>  "Discover the game-changing impact of social media  <span>insights</span> for your business growth",
                    "section_top_description" =>  "Discover the power of our secure and rewarding credit cards.",
                    "section_bottom_description" =>  "Discover the power of our secure and rewarding credit cards.",
                ],

                  "element" => [

                    [
                        "title"       =>  "Social Media Monitor",
                        "description" =>  'Social Media Monitor',
                    ],
             

                    [
                        "title"       =>  "Analytical Reports",
                       "description" =>  'Analytical Reports',
                    ],
                    [
                        "title"       =>  "Template Management",
                        "description" =>  'Template Management',
                    ],
             
                    [
                        "title"       =>  "Feed Analytic",
                        "description" =>  'Feed Analytic',
                    ],
                    [
                        "title"       =>  "AI Content Create",
                        "description" =>  'AI Content Create',
                    ],

                    [
                        "title"       =>  "Feed Analytic",
                        "description" =>  'Feed Analytic',
                    ],

                    [
                        "title"       =>  "Manage profile",
                       "description" =>  'Manage profile',
                    ],
             
                    [
                        "title"       =>  "Manage Post",
                       "description" =>  'Manage Post',
                    ],
             

                ]

            ],


            
            "service_insight" => [

                "content"  => [
                    "title" =>  "Empowering social media <span>insights</span>",
                    "description" => "Discover the power of our secure and rewarding credit cards.",
                ],

                "element" => [

                    [
                        "title"       =>  "Design visually appealing content for all your feeds",
                        "sub_title"       =>  "Manage Accounts",
                        "description" =>  "Take advantage of the in-app integrations with platforms like Canva, Unsplash, and GIPHY. Boost your creative abilities and get access to a wide variety of design elements.",
                    ],
                    [
                        "title"       =>  "Design visually appealing content for all your feeds",
                        "sub_title"       =>  "AI Content",
                        "description" =>  "Take advantage of the in-app integrations with platforms like Canva, Unsplash, and GIPHY. Boost your creative abilities and get access to a wide variety of design elements.",
                    ],
                    [
                        "title"       =>  "Design visually appealing content for all your feeds",
                        "sub_title"       =>  "Create post",
                        "description" =>  "Take advantage of the in-app integrations with platforms like Canva, Unsplash, and GIPHY. Boost your creative abilities and get access to a wide variety of design elements.",
                    ],
                    [
                        "title"       =>  "Design visually appealing content for all your feeds",
                        "sub_title"       =>  "Content",
                        "description" =>  "Take advantage of the in-app integrations with platforms like Canva, Unsplash, and GIPHY. Boost your creative abilities and get access to a wide variety of design elements.",
                    ],
                    [
                        "title"       =>  "Design visually appealing content for all your feeds",
                        "sub_title"       =>  "Insight",
                        "description" =>  "Take advantage of the in-app integrations with platforms like Canva, Unsplash, and GIPHY. Boost your creative abilities and get access to a wide variety of design elements.",
                    ],
                   

                ]

            ],


            
            "team" => [

                "content"  => [

                    "title" => "Meet our <span>team</span>",
                    "sub_title" => "Team",
                    "description" => "Meet our dedicated team of professionals, committed to delivering excellence and innovation. With diverse expertise and a shared passion for success, we work together to achieve our goals and drive our mission forward."
                ],

                "element" => [
                    [
                    ],
                    [
                    ],
                    [
                    ],
                    [
                    ],
                    [
                    ],
                    [
                    ],
                    [
                    ],
                    [
                    ]
                ]

            ],
   
            "template" => [

                "content"  => [

                    "title" => "AI powered social media <span>template</span>",
                    "sub_title" => "Templates",
          
                ]

            ],

    

            "why_us" => [

                "content"  => [

                    "title" => "Watch Your Accounts Grow",
                    "sub_title" => "Why Feedswiz",
                    "button_name"=> "View More",
                    "button_url"=> "@@",
                    "description" => "Track the engagement rate, comments, likes, shares, and impressions for each post, so you know what’s working best for your audience. Once you’ve identified your high-performing posts, you can share them again."
                ],
                "element"  =>  [
                    [
                        "icon" => "bi bi-magic",
                        "title" => "AI Content Generation",
                        "description"=>"Generate captions and images based on prompts, summarize complex content, and turn your product descriptions into highly converting posts.",
                    ],
                    [
                        "icon" => "bi bi-magic",
                        "title" => "AI Content Generation",
                        "description"=>"Generate captions and images based on prompts, summarize complex content, and turn your product descriptions into highly converting posts.",
                    ],
                    [
                        "icon" => "bi bi-magic",
                        "title" => "AI Content Generation",
                        "description"=>"Generate captions and images based on prompts, summarize complex content, and turn your product descriptions into highly converting posts.",
                    ],
                    [
                        "icon" => "bi bi-magic",
                        "title" => "AI Content Generation",
                        "description"=>"Generate captions and images based on prompts, summarize complex content, and turn your product descriptions into highly converting posts.",
                    ],
                    [
                        "icon" => "bi bi-magic",
                        "title" => "AI Content Generation",
                        "description"=>"Generate captions and images based on prompts, summarize complex content, and turn your product descriptions into highly converting posts.",
                    ],
                    [
                        "icon" => "bi bi-magic",
                        "title" => "AI Content Generation",
                        "description"=>"Generate captions and images based on prompts, summarize complex content, and turn your product descriptions into highly converting posts.",
                    ]
                ]

            ],

            "faq" => [

                "content"  => [

                    "title" => "Frequently ask <span>questions</span>",
                    "sub_title" => "FAQS",
                    "description" => "We cant wait for you to explore all of our stories and create your own learning journeys. Before you do, here are the questions we get asked the most by our visitors."
                ],
                "element"  => [

                    [
                        "question" => "Whats it like your job, grab a backpack, and travel the
                        world?",
                        "answer" => "Some of the strangest places on earth are also the most
                        sublime: from the UFO-like dragon's blood trees in Yemen
                        to a rainbow-colored hot spring in Yellowstone to a bridge
                        in Germany that looks like a leftover prop from Lord of
                        the Rings."
                    ],
                    [
                        "question" => "If I visit your country, whats
                        the one meal I shouldnt miss?",
                        "answer" => "Morbi aliquam quis quam in luctus. Nullam tincidunt
                        pulvinar imperdiet. Sed varius, diam vitae posuere semper,
                        libero ex hendrerit nunc, ac sagittis eros metus ut diam.
                        Donec a nibh in libero maximus vehicula. Etiam sit amet
                        condimentum erat. Pellentesque ultrices sagittis turpis,
                        quis tempus ante viverra et.Morbi aliquam quis quam in
                        luctus. Nullam tincidunt pulvinar imperdiet. Sed varius,
                        diam vitae posuere semper, tincidunt pulvinar imperdiet.
                        Sed varius, diam vitae posuere semper."
                    ],
                    [
                        "question" => "What are the most beautiful beaches in the world?",
                        "answer" => "Morbi aliquam quis quam in luctus. Nullam tincidunt
                        pulvinar imperdiet. Sed varius, diam vitae posuere semper,
                        libero ex hendrerit nunc, ac sagittis eros metus ut diam.
                        Donec a nibh in libero maximus vehicula. Etiam sit amet
                        condimentum erat. Pellentesque ultrices sagittis turpis,
                        quis tempus ante viverra et.Morbi aliquam quis quam in
                        luctus. Nullam tincidunt pulvinar imperdiet. Sed varius,
                        diam vitae posuere semper, tincidunt pulvinar imperdiet.
                        Sed varius, diam vitae posuere semper."
                    ],

                    [
                        "question" => "Who s the most interesting person you’ve ever met on a
                        plane",
                        "answer" => "Morbi aliquam quis quam in luctus. Nullam tincidunt
                        pulvinar imperdiet. Sed varius, diam vitae posuere semper,
                        libero ex hendrerit nunc, ac sagittis eros metus ut diam.
                        Donec a nibh in libero maximus vehicula. Etiam sit amet
                        condimentum erat. Pellentesque ultrices sagittis turpis,
                        quis tempus ante viverra et.Morbi aliquam quis quam in
                        luctus. Nullam tincidunt pulvinar imperdiet. Sed varius,
                        diam vitae posuere semper, tincidunt pulvinar imperdiet.
                        Sed varius, diam vitae posuere semper."
                    ],
                   
                ],

            ],
            "plan" => [

                "content"  => [

                    "title" => "Life Planning, Making Easy to Turn r <span>Dreams </span> a Reality",
                    "sub_title" => "Pricing Plan",
                    "button_name"=> "View All",
                    "button_URL"=> "plans",
                    "description" => "We offer flexible pricing plans to suit the diverse needs of our clients."
                ]

            ],

            "testimonial" => [

                "content"  => [

                    "title" => "What our <span>Clients</span> say.",
                    "sub_title" => "Reviews",
                    "description" => "Track the engagement rate, comments, likes, shares, and impressions for each post, so you know what’s working best for your audience"
                ] ,
                "element"  =>  [
                    [
                        "author" => "Sam Wister",
                        "designation" => "Social media manager",
                        "quote" => "I recently got the XYZ Pro, and it's been a game-changer. The performance is top-notch—apps run smoothly, and multitasking is a breeze. The sleek design is a head-turner, and the camera captures stunning shots, even in low light. The battery easily lasts a day, and fast charging is a great perk. My only minor gripe is the fingerprint sensor placement. Overall, a fantastic investment for tech enthusiasts!",
                        "rating" => 3,
           
                    ],
                  
                    [
                        "author" => "Charles Lucas",
                        "designation" => "Social media manager",
                        "quote" => "I recently got the XYZ Pro, and it's been a game-changer. The performance is top-notch—apps run smoothly, and multitasking is a breeze. The sleek design is a head-turner, and the camera captures stunning shots, even in low light. The battery easily lasts a day, and fast charging is a great perk. My only minor gripe is the fingerprint sensor placement. Overall, a fantastic investment for tech enthusiasts!",
                        "rating" => 4,
           
                    ],
                  
                    [
                        "author" => "Winstar",
                        "designation" => "Manager",
                        "quote" => "I recently got the XYZ Pro, and it's been a game-changer. The performance is top-notch—apps run smoothly, and multitasking is a breeze. The sleek design is a head-turner, and the camera captures stunning shots, even in low light. The battery easily lasts a day, and fast charging is a great perk. My only minor gripe is the fingerprint sensor placement. Overall, a fantastic investment for tech enthusiasts!",
                        "rating" => 2,
           
                    ],
                  
                    [
                        "author" => "Mac foster",
                        "designation" => "CEO",
                        "quote" => "I recently got the XYZ Pro, and it's been a game-changer. The performance is top-notch—apps run smoothly, and multitasking is a breeze. The sleek design is a head-turner, and the camera captures stunning shots, even in low light. The battery easily lasts a day, and fast charging is a great perk. My only minor gripe is the fingerprint sensor placement. Overall, a fantastic investment for tech enthusiasts!",
                        "rating" => 5,
           
                    ],
                  
                    [
                        "author" => "Sam Wister",
                        "designation" => "Social media manager",
                        "quote" => "I recently got the XYZ Pro, and it's been a game-changer. The performance is top-notch—apps run smoothly, and multitasking is a breeze. The sleek design is a head-turner, and the camera captures stunning shots, even in low light. The battery easily lasts a day, and fast charging is a great perk. My only minor gripe is the fingerprint sensor placement. Overall, a fantastic investment for tech enthusiasts!",
                        "rating" => 1,
           
                    ],
                  
                  
                ]

            ],

            "blog" => [

                "content"  => [
                    "title" => "News & <span>Blogs</span>",
                    "sub_title" => "Blogs",
                    "button_name"=> "View More",
                    "button_URL"=> "blogs",
                    "description" => "Track the engagement rate, comments, likes, shares, and impressions for each post, so you know what’s working best for your audience. Once you’ve identified your high-performing posts, you can share them again."
                ]

            ],

       

            "social_icon" => [

                "element"  => [
                   
                    [
                        "icon" =>  "bi bi-facebook",
                        "button_url"=> "@@",
                    ],
                    [
                        "icon" =>  "bi bi-linkedin",
                        "button_url"=> "@@",
                    ],
                    [
                        "icon" =>  "bi bi-instagram",
                        "button_url"=> "@@",
                    ],
                    [
                        "icon" =>  "bi bi-twitter",
                        "button_url"=> "@@",
                    ],
                    [
                        "icon" =>  "bi bi-youtube",
                        "button_url"=> "@@",
                    ],
                    [
                        "icon" =>  "bi bi-tiktok",
                        "button_url"=> "@@",
                    ],
                ],
                
            ],

            "footer" => [

                "content"  => [
                   "title"       => "Improve your social media content",
                   "description" => "Lorem ipsum dolor sit amet consectetur adipiscing elit dolor posuere vel venenatis eu sit massa volutpat",
                ],

                
                "element"  => [
                   
                    [
                        "button_name" =>  "Book a demo",
                        "button_URL"=> "@@",
                        "button_icon"=> "bi bi-arrow-up-right",
                    ],
                    [
                        "button_name" =>  "Get Started Free",
                        "button_URL"=> "@@",
                        "button_icon"=> "bi bi-arrow-up-right",
                    ],
             
               
                   
                ],
                
            ],

            "mega_menu" => [

                "content"  => [
                   "select_input" => [
                     "status" => StatusEnum::true->status()
                   ],
                   "title" => "Intregration",
                ],
                
            ],

            "authentication_section" => [

                "content"  => [
                    "description" => "Uncover the untapped potential of your growth to connect with clients."
                ],

                "element" => [

                    [
                        "title"       => "Easy to use dashboard",
                        "description" => "Choose the best of product/service and get a
                        bare metal server at the lowest prices."
                    ],
                    [
                        "title"       => "Easy to use dashboard",
                        "description" => "Choose the best of product/service and get a
                        bare metal server at the lowest prices."
                    ],
                    [
                        "title"       => "Easy to use dashboard",
                        "description" => "Choose the best of product/service and get a
                        bare metal server at the lowest prices."
                    ],
                    [
                        "title"       => "Easy to use dashboard",
                        "description" => "Choose the best of product/service and get a
                        bare metal server at the lowest prices."
                    ],
                ]
                
            ],

        ];

        foreach($sections as $key => $sectionValues){

            if(isset($sectionValues['content'])){
                $insertionKey =  "content_".$key ;
                if(!in_array($insertionKey,$frontendsKeys)){
                    Frontend::create(
                        [
                            'key'   => $insertionKey ,
                            'value' => $sectionValues['content']
                        ]
                   
                    );
                }
            }

            if(isset($sectionValues['element'])){
                $insertionKey =  "element_".$key ;
                if(!in_array($insertionKey,$frontendsKeys)){
                    foreach($sectionValues['element'] as $element){
                        Frontend::create(
                            [
                                'key'   => $insertionKey ,
                                'value' => $element
                            ]
                        );
                    }
                }

            }


        }


        Cache::forget('frontend_content');




    }
}
