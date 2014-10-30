using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace Acsilserver1.ViewModels
{
    public class Info
    {
        public int id { get; set; }
        public string real_path { get; set; }
        public string chosen_path { get; set; }
        public int size { get; set; }
        public string name { get; set; }
        public string path { get; set; }
        public string owner { get; set; }
        public string pseudo_owner { get; set; }
        public string upload_date { get; set; }
        public int is_profile_picture { get; set; }
        public int is_shared { get; set; }
        public string mime_type { get; set; }
        public string formated_size { get; set; }
        public int folder { get; set; }
    }

    public class File
    {
        public Info info { get; set; }
        public object sharedFileUserInfos { get; set; }
    }

    public class Folder
    {
        public int id { get; set; }
        public string real_path { get; set; }
        public string chosen_path { get; set; }
        public int size { get; set; }
        public string name { get; set; }
        public string path { get; set; }
        public string owner { get; set; }
        public string pseudo_owner { get; set; }
        public string upload_date { get; set; }
        public int parent_folder { get; set; }
        public int f_size { get; set; }
    }

    public class User
    {
        public int id { get; set; }
        public string firstname { get; set; }
        public string lastname { get; set; }
        public string username { get; set; }
        public string email { get; set; }
        public string salt { get; set; }
        public string password { get; set; }
        public string usertype { get; set; }
        public string roles { get; set; }
        public string creation_date { get; set; }
        public string picture_account { get; set; }
        public bool is_active { get; set; }
    }

    public class RootObject
    {
        public List<File> files { get; set; }
        public List<Folder> folders { get; set; }
        public List<User> users { get; set; }
    }
}
