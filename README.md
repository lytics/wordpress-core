![image](https://github.com/lytics/wordpress-core/assets/2042008/eea842dc-c763-4878-b8b8-56140a6c54ce)

## Overview
The official Lytics for WordPress plugin. Used to seamlessly integrate the most powerful capabilities of the Lytics Personalization Engine directly into WordPress. Once activated, the plugin will automatically configure the Lytics collection and personalization tag on your website, allowing for the deployment of highly targeted web campaigns, personalized content, recommendations, and more.

## Features
- **Installation:** Seamlessly connect your website with Lytics in a few clicks. No development required.
- **Profile Building / Collection:** Collect comprehensive visitor data from your WordPress site in real-time, enriching Lytics customer profiles for both anonymous and known visitors.
- **Identity Resolution:** Unify user identities across devices and sessions, leveraging Lytics to dynamically personalize site content based on comprehensive user data and segments.
- **Behavioral Insights:** Gain deep insights into user behavior, allowing for dynamic content personalization based on actionable data and user segments identified by Lytics.
- **Content Personalization:** Enhance user experience by dynamically personalizing site content, tailored to individual user preferences and behaviors as understood by Lytics.
- **Content Recommendation:** Improve engagement and user retention through intelligent content recommendations, powered by Lytics' deep learning about user preferences and behaviors.

## Requirements
- WordPress 6.x or higher
- PHP 7.4 or higher
- A valid Lytics account

# Installation
1. **Download the Plugin**: Obtain the latest version of the Lytics plugin from the master branch of this repository.
2. **Install the Plugin**: Transfer all Lytics plugin, including outer `lytics` directory, to your WordPress  `wp-content/plugins` directory. Note: If developing locally this plugin utilizes Vite for its build process, which results in the creation of a `dist` directory. This `dist` directory should be considered the actual plugin directory. Additional details are provided below.
3. **Activate the Plugin**: Use the WordPress admin panel to navigate to the *Plugins* page, locate the `Lytics` plugin, and activate it.

# Configuration
1. **Create Access Token**: Your Lytics [API Token](https://docs.lytics.com/docs/access-tokens#deleting-an-existing-api-token) can be found within your account settings on the Lytics platform. We recommend using the `admin` role.
2. **Enable Plugin**: Access the Lytics plugin settings via the WordPress admin panel. Enter your Lytics Access Token as instructed and click save. Be sure to `enable tag` to ensure Lytics is configured properly.

# Contributing to Lytics for WordPress
As an open source project, we rely on community contributions to continue to improve Lytics WordPress plugin. To contribute, please follow the pre-requisites above and visit our Contributing to Woo doc for more links and contribution guidelines.

## Local Development
The following steps act as a general guid for building and running the Lytics WordPress plugin for local development:

1. **Install Yarn**: Ensure that `yarn` is installed on your development machine.
2. **Repository Cloning**: Clone the Lytics plugin repository to a suitable location outside your WordPress installation.
    ```sh
    git clone https://github.com/lytics/wordpress-core.git
    ```
3. **Dependency Installation**: Navigate to the cloned plugin directory and run `yarn install` to install dependencies.
    ```sh
    cd wordpress-core
    yarn install
    ```
4. **Development Build**: Compile development assets using `yarn`.
    ```sh
    yarn dev
    ```
5. **Symlink Creation**: Establish a symlink in your WordPress `wp-content/plugins/` directory pointing to your development copy of the Lytics plugin. This enables live testing without needing to copy files.
    ```sh
    ln -s /path/to/wordpress-core /path/to/wp-content/plugins/lytics
    ```

## How to Contribute
We welcome contributions to the Lytics WordPress plugin. To contribute:

1. **Fork and Branch**: Fork the repository on GitHub and create a new branch for your feature or bug fix.
2. **Implement Changes**: Make your changes in your branch.
3. **Push Changes**: Push your changes to your GitHub fork.
4. **Submit a Pull Request**: Open a pull request from your fork to the main repository.

## Support
We'd love for you to join our growing community of Lytics developers. Simply visit https://lytics.com/joinslack and join the conversation. 
