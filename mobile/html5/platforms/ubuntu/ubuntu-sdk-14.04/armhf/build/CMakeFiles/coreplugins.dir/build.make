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
include CMakeFiles/coreplugins.dir/depend.make

# Include the progress variables for this target.
include CMakeFiles/coreplugins.dir/progress.make

# Include the compile flags for this target's objects.
include CMakeFiles/coreplugins.dir/flags.make

CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o: CMakeFiles/coreplugins.dir/flags.make
CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o: /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/coreplugins.cpp
	$(CMAKE_COMMAND) -E cmake_progress_report /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles $(CMAKE_PROGRESS_1)
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Building CXX object CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o"
	/usr/bin/arm-linux-gnueabihf-g++   $(CXX_DEFINES) $(CXX_FLAGS) -o CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o -c /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/coreplugins.cpp

CMakeFiles/coreplugins.dir/src/coreplugins.cpp.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing CXX source to CMakeFiles/coreplugins.dir/src/coreplugins.cpp.i"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -E /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/coreplugins.cpp > CMakeFiles/coreplugins.dir/src/coreplugins.cpp.i

CMakeFiles/coreplugins.dir/src/coreplugins.cpp.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling CXX source to assembly CMakeFiles/coreplugins.dir/src/coreplugins.cpp.s"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -S /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/coreplugins.cpp -o CMakeFiles/coreplugins.dir/src/coreplugins.cpp.s

CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.requires:
.PHONY : CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.requires

CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.provides: CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.requires
	$(MAKE) -f CMakeFiles/coreplugins.dir/build.make CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.provides.build
.PHONY : CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.provides

CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.provides.build: CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o: CMakeFiles/coreplugins.dir/flags.make
CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o: /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.console/console.cpp
	$(CMAKE_COMMAND) -E cmake_progress_report /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles $(CMAKE_PROGRESS_2)
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Building CXX object CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o"
	/usr/bin/arm-linux-gnueabihf-g++   $(CXX_DEFINES) $(CXX_FLAGS) -o CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o -c /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.console/console.cpp

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing CXX source to CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.i"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -E /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.console/console.cpp > CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.i

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling CXX source to assembly CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.s"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -S /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.console/console.cpp -o CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.s

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.requires:
.PHONY : CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.requires

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.provides: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.requires
	$(MAKE) -f CMakeFiles/coreplugins.dir/build.make CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.provides.build
.PHONY : CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.provides

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.provides.build: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o: CMakeFiles/coreplugins.dir/flags.make
CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o: /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.device/device.cpp
	$(CMAKE_COMMAND) -E cmake_progress_report /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles $(CMAKE_PROGRESS_3)
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Building CXX object CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o"
	/usr/bin/arm-linux-gnueabihf-g++   $(CXX_DEFINES) $(CXX_FLAGS) -o CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o -c /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.device/device.cpp

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing CXX source to CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.i"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -E /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.device/device.cpp > CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.i

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling CXX source to assembly CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.s"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -S /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build/src/plugins/org.apache.cordova.device/device.cpp -o CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.s

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.requires:
.PHONY : CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.requires

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.provides: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.requires
	$(MAKE) -f CMakeFiles/coreplugins.dir/build.make CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.provides.build
.PHONY : CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.provides

CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.provides.build: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o

CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o: CMakeFiles/coreplugins.dir/flags.make
CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o: coreplugins_automoc.cpp
	$(CMAKE_COMMAND) -E cmake_progress_report /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles $(CMAKE_PROGRESS_4)
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Building CXX object CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o"
	/usr/bin/arm-linux-gnueabihf-g++   $(CXX_DEFINES) $(CXX_FLAGS) -o CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o -c /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/coreplugins_automoc.cpp

CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing CXX source to CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.i"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -E /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/coreplugins_automoc.cpp > CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.i

CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling CXX source to assembly CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.s"
	/usr/bin/arm-linux-gnueabihf-g++  $(CXX_DEFINES) $(CXX_FLAGS) -S /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/coreplugins_automoc.cpp -o CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.s

CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.requires:
.PHONY : CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.requires

CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.provides: CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.requires
	$(MAKE) -f CMakeFiles/coreplugins.dir/build.make CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.provides.build
.PHONY : CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.provides

CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.provides.build: CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o

# Object files for target coreplugins
coreplugins_OBJECTS = \
"CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o" \
"CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o" \
"CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o" \
"CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o"

# External object files for target coreplugins
coreplugins_EXTERNAL_OBJECTS =

libcoreplugins.so: CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o
libcoreplugins.so: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o
libcoreplugins.so: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o
libcoreplugins.so: CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o
libcoreplugins.so: CMakeFiles/coreplugins.dir/build.make
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Widgets.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Location.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Sensors.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Feedback.so.5.0.0
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5SystemInfo.so.5.2.0
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Contacts.so.5.0.0
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Multimedia.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Quick.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5MultimediaWidgets.so.5.2.1
libcoreplugins.so: libcordovaubuntuplugin.so
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Positioning.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5DBus.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Multimedia.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Widgets.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Quick.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Gui.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Qml.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Network.so.5.2.1
libcoreplugins.so: /usr/lib/arm-linux-gnueabihf/libQt5Core.so.5.2.1
libcoreplugins.so: CMakeFiles/coreplugins.dir/link.txt
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --red --bold "Linking CXX shared library libcoreplugins.so"
	$(CMAKE_COMMAND) -E cmake_link_script CMakeFiles/coreplugins.dir/link.txt --verbose=$(VERBOSE)

# Rule to build all files generated by this target.
CMakeFiles/coreplugins.dir/build: libcoreplugins.so
.PHONY : CMakeFiles/coreplugins.dir/build

CMakeFiles/coreplugins.dir/requires: CMakeFiles/coreplugins.dir/src/coreplugins.cpp.o.requires
CMakeFiles/coreplugins.dir/requires: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.console/console.cpp.o.requires
CMakeFiles/coreplugins.dir/requires: CMakeFiles/coreplugins.dir/src/plugins/org.apache.cordova.device/device.cpp.o.requires
CMakeFiles/coreplugins.dir/requires: CMakeFiles/coreplugins.dir/coreplugins_automoc.cpp.o.requires
.PHONY : CMakeFiles/coreplugins.dir/requires

CMakeFiles/coreplugins.dir/clean:
	$(CMAKE_COMMAND) -P CMakeFiles/coreplugins.dir/cmake_clean.cmake
.PHONY : CMakeFiles/coreplugins.dir/clean

CMakeFiles/coreplugins.dir/depend:
	cd /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build && $(CMAKE_COMMAND) -E cmake_depends "Unix Makefiles" /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build /home/galan_g/Development/acsilserver/mobile/html5/platforms/ubuntu/ubuntu-sdk-14.04/armhf/build/CMakeFiles/coreplugins.dir/DependInfo.cmake --color=$(COLOR)
.PHONY : CMakeFiles/coreplugins.dir/depend

