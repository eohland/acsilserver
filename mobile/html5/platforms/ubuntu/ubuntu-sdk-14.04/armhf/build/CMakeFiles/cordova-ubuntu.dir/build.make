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

# Include any dependencies generated for this target.
include CMakeFiles/cordova-ubuntu.dir/depend.make

# Include the progress variables for this target.
include CMakeFiles/cordova-ubuntu.dir/progress.make

# Include the compile flags for this target's objects.
include CMakeFiles/cordova-ubuntu.dir/flags.make

CMakeFiles/cordova-ubuntu.dir/main.cpp.o: CMakeFiles/cordova-ubuntu.dir/flags.make
CMakeFiles/cordova-ubuntu.dir/main.cpp.o: /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/main.cpp
	$(CMAKE_COMMAND) -E cmake_progress_report /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles $(CMAKE_PROGRESS_1)
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Building CXX object CMakeFiles/cordova-ubuntu.dir/main.cpp.o"
	/usr/bin/arm-linux-gnueabihf-g++   $(CXX_DEFINES) $(CXX_FLAGS) -o CMakeFiles/cordova-ubuntu.dir/main.cpp.o -c /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/main.cpp

CMakeFiles/cordova-ubuntu.dir/main.cpp.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing CXX source to CMakeFiles/cordova-ubuntu.dir/main.cpp.i"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -E /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/main.cpp > CMakeFiles/cordova-ubuntu.dir/main.cpp.i

CMakeFiles/cordova-ubuntu.dir/main.cpp.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling CXX source to assembly CMakeFiles/cordova-ubuntu.dir/main.cpp.s"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -S /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/main.cpp -o CMakeFiles/cordova-ubuntu.dir/main.cpp.s

CMakeFiles/cordova-ubuntu.dir/main.cpp.o.requires:
.PHONY : CMakeFiles/cordova-ubuntu.dir/main.cpp.o.requires

CMakeFiles/cordova-ubuntu.dir/main.cpp.o.provides: CMakeFiles/cordova-ubuntu.dir/main.cpp.o.requires
	$(MAKE) -f CMakeFiles/cordova-ubuntu.dir/build.make CMakeFiles/cordova-ubuntu.dir/main.cpp.o.provides.build
.PHONY : CMakeFiles/cordova-ubuntu.dir/main.cpp.o.provides

CMakeFiles/cordova-ubuntu.dir/main.cpp.o.provides.build: CMakeFiles/cordova-ubuntu.dir/main.cpp.o

CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o: CMakeFiles/cordova-ubuntu.dir/flags.make
CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o: cordova-ubuntu_automoc.cpp
	$(CMAKE_COMMAND) -E cmake_progress_report /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles $(CMAKE_PROGRESS_2)
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Building CXX object CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o"
	/usr/bin/arm-linux-gnueabihf-g++   $(CXX_DEFINES) $(CXX_FLAGS) -o CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o -c /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/cordova-ubuntu_automoc.cpp

CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing CXX source to CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.i"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -E /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/cordova-ubuntu_automoc.cpp > CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.i

CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling CXX source to assembly CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.s"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -S /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/cordova-ubuntu_automoc.cpp -o CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.s

CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.requires:
.PHONY : CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.requires

CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.provides: CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.requires
	$(MAKE) -f CMakeFiles/cordova-ubuntu.dir/build.make CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.provides.build
.PHONY : CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.provides

CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.provides.build: CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o

# Object files for target cordova-ubuntu
cordova__ubuntu_OBJECTS = \
"CMakeFiles/cordova-ubuntu.dir/main.cpp.o" \
"CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o"

# External object files for target cordova-ubuntu
cordova__ubuntu_EXTERNAL_OBJECTS =

cordova-ubuntu: CMakeFiles/cordova-ubuntu.dir/main.cpp.o
cordova-ubuntu: CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o
cordova-ubuntu: CMakeFiles/cordova-ubuntu.dir/build.make
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Widgets.so.5.2.1
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Quick.so.5.2.1
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Xml.so.5.2.1
cordova-ubuntu: libcordovaubuntuplugin.so
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Widgets.so.5.2.1
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Quick.so.5.2.1
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Gui.so.5.2.1
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Qml.so.5.2.1
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Network.so.5.2.1
cordova-ubuntu: /usr/lib/arm-linux-gnueabihf/libQt5Core.so.5.2.1
cordova-ubuntu: CMakeFiles/cordova-ubuntu.dir/link.txt
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --red --bold "Linking CXX executable cordova-ubuntu"
	$(CMAKE_COMMAND) -E cmake_link_script CMakeFiles/cordova-ubuntu.dir/link.txt --verbose=$(VERBOSE)

# Rule to build all files generated by this target.
CMakeFiles/cordova-ubuntu.dir/build: cordova-ubuntu
.PHONY : CMakeFiles/cordova-ubuntu.dir/build

CMakeFiles/cordova-ubuntu.dir/requires: CMakeFiles/cordova-ubuntu.dir/main.cpp.o.requires
CMakeFiles/cordova-ubuntu.dir/requires: CMakeFiles/cordova-ubuntu.dir/cordova-ubuntu_automoc.cpp.o.requires
.PHONY : CMakeFiles/cordova-ubuntu.dir/requires

CMakeFiles/cordova-ubuntu.dir/clean:
	$(CMAKE_COMMAND) -P CMakeFiles/cordova-ubuntu.dir/cmake_clean.cmake
.PHONY : CMakeFiles/cordova-ubuntu.dir/clean

CMakeFiles/cordova-ubuntu.dir/depend:
	cd /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build && $(CMAKE_COMMAND) -E cmake_depends "Unix Makefiles" /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles/cordova-ubuntu.dir/DependInfo.cmake --color=$(COLOR)
.PHONY : CMakeFiles/cordova-ubuntu.dir/depend

