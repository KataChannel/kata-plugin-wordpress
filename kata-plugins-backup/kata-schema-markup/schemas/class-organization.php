<?php
/**
 * Organization Schema Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Organization Schema Class
 */
class KataSchema_Organization {
    
    /**
     * Generate Organization schema
     */
    public function generate($post_id = null) {
        $organization_name = get_option('kata_schema_organization_name', get_bloginfo('name'));
        
        if (empty($organization_name)) {
            return false;
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $this->get_organization_type(),
            'name' => $organization_name,
            'url' => home_url('/'),
            'description' => get_bloginfo('description')
        );
        
        // Add logo
        $logo = $this->get_logo_schema();
        if ($logo) {
            $schema['logo'] = $logo;
        }
        
        // Add image (same as logo or different)
        $image = $this->get_image_schema();
        if ($image) {
            $schema['image'] = $image;
        }
        
        // Add contact information
        $contact_point = $this->get_contact_point();
        if ($contact_point) {
            $schema['contactPoint'] = $contact_point;
        }
        
        // Add address
        $address = $this->get_address();
        if ($address) {
            $schema['address'] = $address;
        }
        
        // Add social media profiles
        $social_profiles = $this->get_social_profiles();
        if (!empty($social_profiles)) {
            $schema['sameAs'] = $social_profiles;
        }
        
        // Add founder information
        $founder = $this->get_founder();
        if ($founder) {
            $schema['founder'] = $founder;
        }
        
        // Add founding date
        $founding_date = get_option('kata_schema_organization_founding_date', '');
        if (!empty($founding_date)) {
            $schema['foundingDate'] = $founding_date;
        }
        
        // Add number of employees
        $employee_count = get_option('kata_schema_organization_employee_count', '');
        if (!empty($employee_count)) {
            $schema['numberOfEmployees'] = intval($employee_count);
        }
        
        // Add area served
        $area_served = get_option('kata_schema_organization_area_served', '');
        if (!empty($area_served)) {
            $areas = explode(',', $area_served);
            $schema['areaServed'] = array_map('trim', $areas);
        }
        
        // Add awards
        $awards = get_option('kata_schema_organization_awards', '');
        if (!empty($awards)) {
            $award_list = explode(',', $awards);
            $schema['award'] = array_map('trim', $award_list);
        }
        
        // Add brands
        $brands = $this->get_brands();
        if (!empty($brands)) {
            $schema['brand'] = $brands;
        }
        
        // Add additional properties for specific organization types
        $schema = $this->add_type_specific_properties($schema);
        
        // Apply filters
        $schema = apply_filters('kata_schema_organization', $schema, $post_id);
        
        return $this->clean_schema($schema);
    }
    
    /**
     * Get organization type
     */
    private function get_organization_type() {
        $type = get_option('kata_schema_organization_type', 'Organization');
        
        $valid_types = array(
            'Organization',
            'Corporation',
            'LocalBusiness',
            'Restaurant',
            'Store',
            'EducationalOrganization',
            'GovernmentOrganization',
            'NGO',
            'PerformingGroup',
            'SportsOrganization',
            'MedicalOrganization',
            'NewsMediaOrganization'
        );
        
        return in_array($type, $valid_types) ? $type : 'Organization';
    }
    
    /**
     * Get logo schema
     */
    private function get_logo_schema() {
        $logo_url = get_option('kata_schema_organization_logo', '');
        
        if (empty($logo_url)) {
            // Try to get custom logo from theme
            $custom_logo_id = get_theme_mod('custom_logo');
            if ($custom_logo_id) {
                $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
            }
        }
        
        if (empty($logo_url)) {
            return false;
        }
        
        $logo_schema = array(
            '@type' => 'ImageObject',
            'url' => $logo_url
        );
        
        // Add logo dimensions if available
        if ($custom_logo_id) {
            $logo_meta = wp_get_attachment_metadata($custom_logo_id);
            if (isset($logo_meta['width']) && isset($logo_meta['height'])) {
                $logo_schema['width'] = $logo_meta['width'];
                $logo_schema['height'] = $logo_meta['height'];
            }
        }
        
        return $logo_schema;
    }
    
    /**
     * Get image schema
     */
    private function get_image_schema() {
        $image_url = get_option('kata_schema_organization_image', '');
        
        if (empty($image_url)) {
            // Use logo as fallback
            $logo = $this->get_logo_schema();
            return $logo ? $logo['url'] : false;
        }
        
        return array(
            '@type' => 'ImageObject',
            'url' => $image_url
        );
    }
    
    /**
     * Get contact point
     */
    private function get_contact_point() {
        $phone = get_option('kata_schema_organization_phone', '');
        $email = get_option('kata_schema_organization_email', '');
        $contact_type = get_option('kata_schema_organization_contact_type', 'customer service');
        
        if (empty($phone) && empty($email)) {
            return false;
        }
        
        $contact_point = array(
            '@type' => 'ContactPoint',
            'contactType' => $contact_type
        );
        
        if (!empty($phone)) {
            $contact_point['telephone'] = $phone;
        }
        
        if (!empty($email)) {
            $contact_point['email'] = $email;
        }
        
        // Add available languages
        $languages = get_option('kata_schema_organization_languages', '');
        if (!empty($languages)) {
            $language_array = explode(',', $languages);
            $contact_point['availableLanguage'] = array_map('trim', $language_array);
        }
        
        // Add hours available
        $hours = get_option('kata_schema_organization_contact_hours', '');
        if (!empty($hours)) {
            $contact_point['hoursAvailable'] = array(
                '@type' => 'OpeningHoursSpecification',
                'description' => $hours
            );
        }
        
        return $contact_point;
    }
    
    /**
     * Get address
     */
    private function get_address() {
        $street = get_option('kata_schema_organization_street_address', '');
        $city = get_option('kata_schema_organization_city', '');
        $state = get_option('kata_schema_organization_state', '');
        $postal_code = get_option('kata_schema_organization_postal_code', '');
        $country = get_option('kata_schema_organization_country', '');
        
        if (empty($street) && empty($city)) {
            return false;
        }
        
        $address = array(
            '@type' => 'PostalAddress'
        );
        
        if (!empty($street)) {
            $address['streetAddress'] = $street;
        }
        
        if (!empty($city)) {
            $address['addressLocality'] = $city;
        }
        
        if (!empty($state)) {
            $address['addressRegion'] = $state;
        }
        
        if (!empty($postal_code)) {
            $address['postalCode'] = $postal_code;
        }
        
        if (!empty($country)) {
            $address['addressCountry'] = $country;
        }
        
        return $address;
    }
    
    /**
     * Get social profiles
     */
    private function get_social_profiles() {
        $profiles = get_option('kata_schema_social_profiles', array());
        
        if (!is_array($profiles)) {
            return array();
        }
        
        return array_filter($profiles, function($url) {
            return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
        });
    }
    
    /**
     * Get founder information
     */
    private function get_founder() {
        $founder_name = get_option('kata_schema_organization_founder_name', '');
        
        if (empty($founder_name)) {
            return false;
        }
        
        $founder = array(
            '@type' => 'Person',
            'name' => $founder_name
        );
        
        $founder_url = get_option('kata_schema_organization_founder_url', '');
        if (!empty($founder_url)) {
            $founder['url'] = $founder_url;
        }
        
        return $founder;
    }
    
    /**
     * Get brands
     */
    private function get_brands() {
        $brands_string = get_option('kata_schema_organization_brands', '');
        
        if (empty($brands_string)) {
            return array();
        }
        
        $brand_names = explode(',', $brands_string);
        $brands = array();
        
        foreach ($brand_names as $brand_name) {
            $brand_name = trim($brand_name);
            if (!empty($brand_name)) {
                $brands[] = array(
                    '@type' => 'Brand',
                    'name' => $brand_name
                );
            }
        }
        
        return $brands;
    }
    
    /**
     * Add type-specific properties
     */
    private function add_type_specific_properties($schema) {
        $type = $schema['@type'];
        
        switch ($type) {
            case 'LocalBusiness':
            case 'Restaurant':
            case 'Store':
                // Add opening hours
                $opening_hours = $this->get_opening_hours();
                if ($opening_hours) {
                    $schema['openingHoursSpecification'] = $opening_hours;
                }
                
                // Add price range
                $price_range = get_option('kata_schema_organization_price_range', '');
                if (!empty($price_range)) {
                    $schema['priceRange'] = $price_range;
                }
                
                // Add geo coordinates
                $geo = $this->get_geo_coordinates();
                if ($geo) {
                    $schema['geo'] = $geo;
                }
                break;
                
            case 'Restaurant':
                // Add cuisine
                $cuisine = get_option('kata_schema_organization_cuisine', '');
                if (!empty($cuisine)) {
                    $schema['servesCuisine'] = $cuisine;
                }
                
                // Add menu
                $menu_url = get_option('kata_schema_organization_menu_url', '');
                if (!empty($menu_url)) {
                    $schema['hasMenu'] = $menu_url;
                }
                break;
                
            case 'EducationalOrganization':
                // Add alumni
                $alumni = get_option('kata_schema_organization_alumni', '');
                if (!empty($alumni)) {
                    $alumni_list = explode(',', $alumni);
                    $schema['alumni'] = array_map('trim', $alumni_list);
                }
                break;
        }
        
        return $schema;
    }
    
    /**
     * Get opening hours
     */
    private function get_opening_hours() {
        $hours = array();
        
        $days = array(
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        );
        
        foreach ($days as $key => $day) {
            $opens = get_option("kata_schema_organization_hours_{$key}_open", '');
            $closes = get_option("kata_schema_organization_hours_{$key}_close", '');
            $closed = get_option("kata_schema_organization_hours_{$key}_closed", false);
            
            if (!$closed && !empty($opens) && !empty($closes)) {
                $hours[] = array(
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => $day,
                    'opens' => $opens,
                    'closes' => $closes
                );
            }
        }
        
        return $hours;
    }
    
    /**
     * Get geo coordinates
     */
    private function get_geo_coordinates() {
        $latitude = get_option('kata_schema_organization_latitude', '');
        $longitude = get_option('kata_schema_organization_longitude', '');
        
        if (empty($latitude) || empty($longitude)) {
            return false;
        }
        
        return array(
            '@type' => 'GeoCoordinates',
            'latitude' => floatval($latitude),
            'longitude' => floatval($longitude)
        );
    }
    
    /**
     * Clean schema by removing empty values
     */
    private function clean_schema($schema) {
        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $schema[$key] = $this->clean_schema($value);
                if (empty($schema[$key])) {
                    unset($schema[$key]);
                }
            } elseif (empty($value) && $value !== 0 && $value !== '0') {
                unset($schema[$key]);
            }
        }
        
        return $schema;
    }
}
