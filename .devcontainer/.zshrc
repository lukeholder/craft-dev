autoload -Uz compinit && compinit

export FLYCTL_INSTALL=~/.fly
export PATH=$FLYCTL_INSTALL/bin:$PATH

autoload -U colors && colors
autoload -Uz vcs_info
precmd() { vcs_info }
zstyle ':vcs_info:git:*' formats '[%b]'
setopt prompt_subst
export PROMPT='%{$fg[blue]%}%n@%m %{$fg[magenta]%}%1~ %{$fg[yellow]%}[%*] %{$fg[green]%}${vcs_info_msg_0_}%{$fg[red]%}%(?..[exit: %?])%{$reset_color%} %# '
setopt histignorealldups sharehistory

# Use emacs keybindings even if our EDITOR is set to vi
bindkey -e

# Keep 1000 lines of history within the shell and save it to ~/.zsh_history:
HISTSIZE=100000
SAVEHIST=100000
HISTFILE=~/.zsh_history

# Use modern completion system
if [ -e /usr/share/zsh/site-functions ] ; then
  FPATH=/usr/share/zsh/site-functions:${FPATH}
fi
autoload -Uz compinit
compinit

zstyle ':completion:*' auto-description 'specify: %d'
zstyle ':completion:*' completer _expand _complete _correct _approximate
zstyle ':completion:*' format 'Completing %d'
zstyle ':completion:*' group-name ''
zstyle ':completion:*' menu select=2
eval "$(dircolors -b)"
zstyle ':completion:*:default' list-colors ${(s.:.)LS_COLORS}
zstyle ':completion:*' list-colors ''
zstyle ':completion:*' list-prompt %SAt %p: Hit TAB for more, or the character to insert%s
zstyle ':completion:*' matcher-list '' 'm:{a-z}={A-Z}' 'm:{a-zA-Z}={A-Za-z}' 'r:|[._-]=* r:|=* l:|=*'
zstyle ':completion:*' menu select=long
zstyle ':completion:*' select-prompt %SScrolling active: current selection at %p%s
zstyle ':completion:*' use-compctl false
zstyle ':completion:*' verbose true

zstyle ':completion:*:*:kill:*:processes' list-colors '=(#b) #([0-9]#)*=0=01;31'
zstyle ':completion:*:kill:*' command 'ps -u $USER -o pid,%cpu,tty,cputime,cmd'

# funcs

function set-title() {
    local title=${@:-$(basename "$(pwd)")}
    if [ "${TERM_PROGRAM}" = "tmux" ] ; then
	    tmux select-pane -T ${title}
    fi
}

function randchars() {
    local bytes=${1:-32}
    </dev/urandom head -c $(( ${bytes} / 2 )) | hexdump -e '"%x"' |pbcopy; pbpaste|wc ; pbpaste
}
