# CMAKE generated file: DO NOT EDIT!
# Generated by "Unix Makefiles" Generator, CMake Version 2.8

#=============================================================================
# Special targets provided by cmake.

# Disable implicit rules so canonical targets will work.
.SUFFIXES:

# Remove some rules from gmake that .SUFFIXES does not remove.
SUFFIXES =

.SUFFIXES: .hpux_make_needs_suffix_list

# Suppress display of executed commands.
$(VERBOSE).SILENT:

# A target that is always out of date.
cmake_force:
.PHONY : cmake_force

#=============================================================================
# Set environment variables for the build.

# The shell in which to execute make rules.
SHELL = /bin/sh

# The CMake executable.
CMAKE_COMMAND = /usr/bin/cmake

# The command to remove a file.
RM = /usr/bin/cmake -E remove -f

# Escaping for special characters.
EQUALS = =

# The top-level source directory on which CMake was run.
CMAKE_SOURCE_DIR = /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build

# The top-level build directory on which CMake was run.
CMAKE_BINARY_DIR = /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build

# Utility rule file for cordova-ubuntu_automoc.

# Include the progress variables for this target.
include CMakeFiles/cordova-ubuntu_automoc.dir/progress.make

CMakeFiles/cordova-ubuntu_automoc:
	$(CMAKE_COMMAND) -E cmake_progress_report /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles $(CMAKE_PROGRESS_1)
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --blue --bold "Automoc for target cordova-ubuntu"
	/usr/bin/cmake -E cmake_automoc /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles/cordova-ubuntu_automoc.dir/ Release

cordova-ubuntu_automoc: CMakeFiles/cordova-ubuntu_automoc
cordova-ubuntu_automoc: CMakeFiles/cordova-ubuntu_automoc.dir/build.make
.PHONY : cordova-ubuntu_automoc

# Rule to build all files generated by this target.
CMakeFiles/cordova-ubuntu_automoc.dir/build: cordova-ubuntu_automoc
.PHONY : CMakeFiles/cordova-ubuntu_automoc.dir/build

CMakeFiles/cordova-ubuntu_automoc.dir/clean:
	$(CMAKE_COMMAND) -P CMakeFiles/cordova-ubuntu_automoc.dir/cmake_clean.cmake
.PHONY : CMakeFiles/cordova-ubuntu_automoc.dir/clean

CMakeFiles/cordova-ubuntu_automoc.dir/depend:
	cd /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build && $(CMAKE_COMMAND) -E cmake_depends "Unix Makefiles" /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles/cordova-ubuntu_automoc.dir/DependInfo.cmake --color=$(COLOR)
.PHONY : CMakeFiles/cordova-ubuntu_automoc.dir/depend

