{
  description = "balance - Project management that doesn't suck (PHP edition).";
  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-unstable";
  };
  outputs = {
    self,
    nixpkgs,
  }: let
    systems = ["x86_64-linux" "aarch64-linux"];

    forAllSystems = fn: nixpkgs.lib.genAttrs systems (system: fn nixpkgs.legacyPackages.${system});
  in {
    devShells = forAllSystems (pkgs: {
      default = pkgs.mkShell {
        packages = [
          (pkgs.php.withExtensions ({
            enabled,
            all,
          }:
            enabled ++ [all.pdo all.sqlite3]))
          pkgs.sqlite
          pkgs.intelephense
        ];
        shellHook = ''
          export PS1="(balance-dev) $PS1"
        '';
      };
    });

    formatter = forAllSystems (pkgs: pkgs.alejandra);
  };
}
